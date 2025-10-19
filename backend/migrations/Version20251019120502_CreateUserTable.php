<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251019120502_CreateUserTable extends AbstractMigration {

  public function up(Schema $schema): void {
    $this->addSql('CREATE TABLE "user" (
                  id UUID NOT NULL,
                  username VARCHAR(180) NOT NULL,
                  email VARCHAR(180) NOT NULL,
                  roles JSON NOT NULL,
                  password VARCHAR(255) NOT NULL,
                  PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
  }

  public function down(Schema $schema): void {
    $this->addSql('CREATE SCHEMA public');
    $this->addSql('DROP TABLE "user"');
  }
}
