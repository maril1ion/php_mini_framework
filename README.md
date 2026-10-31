# PHP Mini Framework
Что это: учебный HTTP-фреймворк на чистом PHP (в процессе).
What it is this: a training HTTP-framework in pure php (in progress).

## Requirements
Docker, Docker Compose

## Quick start
docker compose up -d\
curl http://localhost:8080/health

## Services / ports
8080 — app (nginx), 3307 — MySQL, 8025 — Mailpit UI

## Structure
public/ — entry point, src/ — code, docker/ — env configs

## Tests
docker compose exec app ./vendor/bin/pest
