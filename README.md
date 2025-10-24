# Chat Messenger Application

A full-stack real-time chat application built with **Symfony**, **Angular**, and **Mercure**.  
It includes JWT authentication, refresh token flow, plugin-based filter and auto-response, and real-time updates via Mercure.

---

## Project Overview

The project demonstrates a modular backend and frontend setup for a chat system that supports:
- Secure **JWT authentication** with refresh token rotation
- **Plugin-based architecture** for message auto-responses and filters
- Real-time communication using **Symfony Mercure Hub**
- **Angular frontend** that consumes the backend API
- Fully Dockerized environment for simple local setup

---

## Prerequisites

Ensure you have the following installed:
- Docker & Docker Compose
- Node.js (v18 or higher) and npm
- Git

---

## Getting Started

### 1. Clone the repository
```bash
  git clone https://github.com/danicavuck/messenger-app.git
  cd messenger-app
```

### 2. Environment setup

Copy and rename the `.env.dist` file in the backend:
```bash
  cp backend/.env.dist backend/.env
```
You can adjust environment variables (e.g. ports, DB credentials) if needed.

### Generate JWT keys (first time only)
Run these commands on your host machine:

```bash
  mkdir -p backend/config/jwt
  openssl genpkey -algorithm RSA -out backend/config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
  openssl pkey -in backend/config/jwt/private.pem -out backend/config/jwt/public.pem -pubout
  chmod 644 backend/config/jwt/private.pem backend/config/jwt/public.pem
```
These keys are used to sign and verify JWT tokens for authentication.

### 3. Start all services
```bash
  docker compose up --build
```
This command will build and start:
- Symfony backend (`messenger-backend`)
- PostgreSQL database (`messenger-postgres`)
- Mercure Hub (`messenger-mercure`)

After successful startup:
- Backend API: http://localhost:8080  
- Mercure Hub: http://localhost:3000/.well-known/mercure

---

## Backend Details

**Tech stack:**
- Symfony 7 (PHP 8.3)
- Doctrine ORM (PostgreSQL)
- LexikJWTAuthenticationBundle for JWT
- Mercure for async updates

### Useful commands

Run migrations:
```bash
  docker exec -it messenger-backend php bin/console doctrine:migrations:migrate
```

Check DB schema:
```bash
  docker exec -it messenger-backend php bin/console doctrine:schema:validate
```

View logs:
```bash
  docker logs -f messenger-backend
```

Access container shell:
```bash
  docker exec -it messenger-backend bash
```

---

## API Routes

The backend exposes the following REST API endpoints:

### Authentication & User
| Method | Endpoint | Description |
|---------|-----------|-------------|
| `POST` | `/api/v1/auth/register` | Register a new user |
| `POST` | `/api/v1/auth/login` | Log in and obtain JWT token |
| `POST` | `/api/v1/auth/refresh` | Refresh access token |
| `GET`  | `/api/v1/users/me` | Retrieve authenticated user profile |

### Messages
| Method | Endpoint | Description |
|---------|-----------|-------------|
| `GET`  | `/api/v1/messages` | List all messages |
| `POST` | `/api/v1/messages` | Create a new message |

---

## Default User Credentials

A demo user is preloaded via fixtures:

```
email: demo@example.com
password: demo1234
```

Use this account to log in and start chatting immediately.

---

## Plugin System

###  Profanity Filter Plugin
Automatically censors offensive words in messages before they are saved.

**Blocked words:**  
`['badword', 'idiot', 'stupid', 'javascript']`

Example:  
`"You are an idiot"` → `"You are an ***"`

###  Auto-Response Plugin
Sends automatic replies to casual greetings.

**Triggers:**  
`hi`, `hello`, `hey`

Example:  
User: `"hi"`  
Bot: `"Hi there! 🤖"`

---

## Frontend Details

**Tech stack:**
- Angular 17
- Signals API and Reactive State
- Tailwind CSS for UI styling

### Run the Angular app locally

In another terminal:
```bash
  cd frontend
  npm install
  npm start
```
Angular dev server runs on:
```
http://localhost:4200
```

The frontend communicates with the backend at `http://localhost:8080` and subscribes to Mercure for real-time message updates.

---

## Docker Services Overview

| Service             | Description                        | Port |
|----------------------|------------------------------------|------|
| messenger-backend    | Symfony PHP container               | 8080 |
| messenger-postgres   | PostgreSQL 17 database              | 5432 |
| messenger-mercure    | Mercure Hub for real-time events    | 3000 |

---

## Healthcheck & Entrypoint Script

The backend includes an automatic setup script that:
1. Installs dependencies
2. Runs Doctrine migrations
3. Loads fixtures (if in dev mode)
4. Starts Apache server

Health check endpoint:
```
GET http://localhost:8080/api/v1/health
```

---

## Common Development Commands

Rebuild everything from scratch:
```bash
  docker compose down -v
  docker compose up --build
```

Reset database:
```bash
  docker exec -it messenger-backend php bin/console doctrine:database:drop --force
  docker exec -it messenger-backend php bin/console doctrine:database:create
  docker exec -it messenger-backend php bin/console doctrine:migrations:migrate
```

---

## License

This project is provided for educational and demonstration purposes only.
