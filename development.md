# Development Guide - Diet Calculator

## Project Idea (Short)
This app is a diet/menu planner that manages foods, nutrients, recipes, and menu plans by day and meal. Authenticated users can create and edit Foods, Nutrients, Recipes, Menu Plans, Menu Days, and Meal Items.

## Architecture (Short)
- Backend: Laravel (MVC) with standard auth.
- Database: PostgreSQL.
- Frontend: Vite + Tailwind CSS + Alpine.js.
- Web server: Nginx.
- Containerization: Docker + Docker Compose.
- Production: Nginx + Certbot for SSL.

Services (local):
- `app` - PHP/Laravel
- `pgsql` - PostgreSQL
- `nginx` - reverse proxy
- `node` - Vite dev server

Services (prod):
- `app` - PHP/Laravel (php-fpm)
- `pgsql` - PostgreSQL
- `nginx` - reverse proxy (80/443)
- `certbot` - SSL renew

## Project Structure (Key Paths)
- `app/` - business logic, controllers, models
- `routes/web.php` - application routes
- `resources/` - views, assets
- `database/migrations` - DB schema
- `database/seeders` - seeders (includes CSV for foods)
- `docker/` - Nginx configs
- `docker-compose.yml` - local
- `docker-compose.prod.yml` - production
- `deploy.sh` - production deploy with SSL

## Environment Configuration
Local:
- use `.env.docker` as a base and copy it to `.env`

Production:
- fill in `.env.production` (DB, APP_KEY, etc.)
- `deploy.sh` reads `.env.production`

Important:
- In production, set `APP_KEY` explicitly in `.env.production` and keep it stable. Avoid generating a new key on every container start, otherwise sessions/cookies and encrypted data can break.

## Local Setup and Development (Docker)
Recommended first-time setup:
- `make install`
  - copies `.env.docker` to `.env`
  - starts containers
  - runs `composer install`
  - generates `APP_KEY`
  - runs migrations + seeders

Common commands:
- `make up` - start containers
- `make down` - stop containers
- `make restart` - rebuild + restart
- `make logs` - logs
- `make ps` - status
- `make artisan cmd="..."` - artisan commands
- `make test` - run tests
- `make sh` - shell in app container

Local URL:
- http://localhost

## Seed Data
Seeders available:
- `FoodsFromCsvSeeder` uses `database/seeders/data/foods.csv`
- `MealTypeSeeder`

Notes:
- In production, seeding on every container restart is not recommended unless your seeders are idempotent. Prefer running seeders once (first deploy) or ensure they do not duplicate data.

## Production Deployment
Server prerequisites:
- Docker + Docker Compose installed
- Ports 80/443 open

Production volume strategy (important):
- Use a single shared volume `app_code` mounted to `/var/www` for BOTH `app` and `nginx`.
- Use a separate volume `storage` for `/var/www/storage/app`.
- The production image should include:
  - `vendor/` (via `composer install --no-dev` during Docker build)
  - `public/build` (via `npm run build` during Docker build)
- The production `entrypoint.sh` should bootstrap the persistent volume:
  - If `/var/www/artisan` is missing (empty volume), copy app files from the image (e.g. `/app`) into `/var/www`
  - Then run migrations/cache (and optionally seeders) and start php-fpm

Steps:
1. Configure `.env.production` with real credentials (DB, `APP_KEY`, etc.).
2. Run deploy:
   - `make deploy` or `bash deploy.sh`
3. The script will:
   - build images
   - start Nginx + DB
   - obtain SSL certificate (if missing)
   - start all containers

Useful production commands:
- `make prod-ps`
- `make prod-logs`
- `make prod-restart`
- `make prod-down`
- `make ssl-renew`

## Server Access
1. SSH into the server:
   - `ssh user@192.1.1.0`
2. Go to the project directory (e.g. `/path/to/diet-calculator`).
3. Use `make prod-*` commands for operations.

## Frontend Dev Server (Vite)
Locally, the `node` service runs:
- `npm run dev -- --host 0.0.0.0`

If you run it manually:
- `make npm cmd="install"`
- `make npm cmd="run dev -- --host 0.0.0.0"`

## Notes
- If you see containers restarting in production, check logs first:
  - `docker logs --tail=200 diet-calculator-app-1`
  - `docker logs --tail=200 diet-calculator-nginx-1`
- Common production issues are missing `vendor/` (composer not run during build) or an empty mounted volume at `/var/www` without bootstrap copy logic.
