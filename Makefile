# ==============================================================================
# Diet Calculator — Docker Makefile
# ==============================================================================
# Local:      make up / make down / make restart
# Production: make prod-up / make prod-down / make deploy
# ==============================================================================

PROD = -f docker-compose.prod.yml

# ------------------------------------------------------------------------------
# Local development
# ------------------------------------------------------------------------------

install: ## First-time local setup
	cp -n .env.docker .env || true
	docker compose up -d --build
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	docker compose exec app php artisan storage:link
	docker compose exec app php artisan db:seed --class=FoodsFromCsvSeeder
	docker compose exec app php artisan db:seed --class=MealTypeSeeder

up: ## Start local containers
	docker compose up -d --build

down: ## Stop local containers
	docker compose down

restart: ## Restart local containers
	docker compose down
	docker compose up -d --build

logs: ## Tail local logs
	docker compose logs -f

ps: ## Show container status
	docker compose ps

# ------------------------------------------------------------------------------
# Laravel commands (local)
# ------------------------------------------------------------------------------

artisan: ## Run artisan command: make artisan cmd="migrate:status"
	docker compose exec app php artisan $(cmd)

migrate: ## Run migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## Drop all tables and re-migrate
	docker compose exec app php artisan migrate:fresh

migrate-status: ## Show migration status
	docker compose exec app php artisan migrate:status

seed: ## Run all seeders
	docker compose exec app php artisan db:seed --force

seed-foods: ## Seed foods from CSV
	docker compose exec app php artisan db:seed --class=FoodsFromCsvSeeder --force

seed-meals: ## Seed meal types
	docker compose exec app php artisan db:seed --class=MealTypeSeeder --force

tinker: ## Open Laravel tinker
	docker compose exec app php artisan tinker

cache: ## Cache config, routes, views
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache

cache-clear: ## Clear all caches
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	docker compose exec app php artisan cache:clear

# ------------------------------------------------------------------------------
# Composer & NPM (local)
# ------------------------------------------------------------------------------

composer: ## Run composer command: make composer cmd="require package"
	docker compose exec app composer $(cmd)

npm: ## Run npm command: make npm cmd="install package"
	docker compose exec node npm $(cmd)

# ------------------------------------------------------------------------------
# Testing (local)
# ------------------------------------------------------------------------------

test: ## Run PHPUnit tests
	docker compose exec app php artisan test

# ------------------------------------------------------------------------------
# Container shell (local)
# ------------------------------------------------------------------------------

sh: ## Open shell in app container
	docker compose exec app sh

sh-db: ## Open psql shell
	docker compose exec pgsql psql -U diet_user -d diet_calculator

# ------------------------------------------------------------------------------
# Production
# ------------------------------------------------------------------------------

prod-up: ## Start production containers
	docker compose $(PROD) up -d --build

prod-down: ## Stop production containers
	docker compose $(PROD) down

prod-restart: ## Restart production containers
	docker compose $(PROD) down
	docker compose $(PROD) up -d --build

prod-logs: ## Tail production logs
	docker compose $(PROD) logs -f

prod-ps: ## Show production container status
	docker compose $(PROD) ps

prod-sh: ## Open shell in production app container
	docker compose $(PROD) exec app sh

deploy: ## Full production deployment with SSL
	bash deploy.sh

ssl-renew: ## Renew SSL certificate
	docker compose $(PROD) run --rm certbot renew
	docker compose $(PROD) exec nginx nginx -s reload

prod-restart:
	docker compose -f docker-compose.prod.yml build --no-cache app && docker compose -f docker-compose.prod.yml up -d --force-recreate app nginx

# ------------------------------------------------------------------------------
# Help
# ------------------------------------------------------------------------------

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.PHONY: install up down restart logs ps artisan migrate migrate-fresh migrate-status seed seed-foods seed-meals tinker cache cache-clear composer npm test sh sh-db prod-up prod-down prod-restart prod-logs prod-ps prod-sh deploy ssl-renew help
