# ══════════════════════════════════════════════════════════════
#  Sistem Penomoran SK — Makefile
#  Usage: make <target>
# ══════════════════════════════════════════════════════════════

.PHONY: help up down restart build shell logs migrate seed fresh tinker test \
        clear cache optimize ps env-setup

# Default target
help: ## Show this help
	@echo ""
	@echo "  🚀 Sistem Penomoran SK — Docker Commands"
	@echo "  ════════════════════════════════════════"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@echo ""

# ── Docker Lifecycle ───────────────────────────────────────────
up: ## Start all containers (detached)
	/usr/local/bin/docker compose up -d
	@echo "✅ App running at http://localhost"

up-dev: ## Start all containers including Vite dev server
	/usr/local/bin/docker compose --profile dev up -d
	@echo "✅ App running at http://localhost | Vite at http://localhost:5173"

down: ## Stop and remove containers
	/usr/local/bin/docker compose down

restart: ## Restart all containers
	/usr/local/bin/docker compose restart

build: ## Build / rebuild Docker image
	/usr/local/bin/docker compose build --no-cache

ps: ## Show running containers status
	/usr/local/bin/docker compose ps

logs: ## Tail all container logs
	/usr/local/bin/docker compose logs -f

logs-app: ## Tail app (Laravel) logs only
	/usr/local/bin/docker compose logs -f app

logs-nginx: ## Tail Nginx logs only
	/usr/local/bin/docker compose logs -f nginx

# ── Setup ──────────────────────────────────────────────────────
env-setup: ## Copy .env.docker to .env (for Docker use)
	@if [ ! -f .env ]; then \
		cp .env.docker .env; \
		echo "✅ .env created from .env.docker"; \
	else \
		echo "⚠️  .env already exists. Edit manually if needed."; \
	fi

key: ## Generate application key (run after env-setup)
	/usr/local/bin/docker compose exec app php artisan key:generate

# ── Database ───────────────────────────────────────────────────
migrate: ## Run database migrations
	/usr/local/bin/docker compose exec app php artisan migrate --force

seed: ## Run database seeders
	/usr/local/bin/docker compose exec app php artisan db:seed --force

fresh: ## Drop all tables, run migrations & seed (⚠ destroys data!)
	/usr/local/bin/docker compose exec app php artisan migrate:fresh --seed --force

# ── Laravel ────────────────────────────────────────────────────
shell: ## Open a shell inside the app container
	/usr/local/bin/docker compose exec app bash

tinker: ## Open Laravel Tinker REPL
	/usr/local/bin/docker compose exec app php artisan tinker

optimize: ## Cache config, routes, events, views
	/usr/local/bin/docker compose exec app php artisan optimize

clear: ## Clear all Laravel caches
	/usr/local/bin/docker compose exec app php artisan optimize:clear

test: ## Run PHPUnit tests
	/usr/local/bin/docker compose exec app php artisan test

queue: ## Start queue worker inside container
	/usr/local/bin/docker compose exec app php artisan queue:work --sleep=3 --tries=3 --max-time=3600
