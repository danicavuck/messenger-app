# Messenger Backend – Symfony 7 + Docker (Development)

This setup runs the Symfony 7.2 backend in development mode using Docker with PHP 8.4, Apache, and PostgreSQL 17.

------------------------------------------------------------
Requirements
------------------------------------------------------------

- Docker & Docker Compose v2.17+
- Git installed
- Ports 8080 and 5432 available

------------------------------------------------------------
Development Commands
------------------------------------------------------------

Build and start containers:
```bash
  docker compose -f docker-compose.yml up --build
```

Access backend in browser:
http://localhost:8080

Access PHP container:
```bash
  docker exec -it messenger-backend bash
```

Access PostgreSQL:
Host: localhost
Port: 5432
User: admin
Password: admin
Database: messenger_db

Run Symfony commands inside container:
```bash
  docker exec -it messenger-backend php bin/console doctrine:database:create
```

Execute project migrations
```bash
  docker exec -it messenger-backend php bin/console doctrine:migrations:migrate
```
Clear cache
```bash
  docker exec -it messenger-backend php bin/console cache:clear
```


Stop containers:
```bash
   docker compose -f docker-compose.yml down
```

Full reset (remove database volume):
```bash
  docker compose -f docker-compose.dev.yml down -v
```

Generate JWT keys
```shell
  docker exec -it messenger-backend php bin/console lexik:jwt:generate-keypair
```
