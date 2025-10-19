<?php

namespace App\Resolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class RequestValidator implements ValueResolverInterface {
  public function __construct(private ValidatorInterface $validator) {}

  public function resolve(Request $request, ArgumentMetadata $argument): iterable {
    $class = $argument->getType();

    if (!$class || !str_starts_with($class, 'App\\DTO\\')) {
      return [];
    }

    $data = json_decode($request->getContent(), true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
      throw new BadRequestHttpException('Invalid JSON payload.');
    }

    $dto = new $class();
    foreach ($data as $key => $value) {
      if (property_exists($dto, $key)) {
        $dto->$key = $value;
      }
    }

    $errors = $this->validator->validate($dto);
    if (count($errors) > 0) {
      throw new BadRequestHttpException((string) $errors);
    }

    return [$dto];
  }
}
