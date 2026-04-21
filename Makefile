# ══════════════════════════════════════════════════════════════
#  Sistem Penomoran SK — Makefile
#  Usage : make <target>
#  Syarat: Docker & Docker Compose sudah terinstall
# ══════════════════════════════════════════════════════════════

DOCKER_COMPOSE := docker compose
PHP            := $(DOCKER_COMPOSE) exec app php
ARTISAN        := $(PHP) artisan

.PHONY: help up up-dev down restart build rebuild ps \
        logs logs-app logs-nginx \
        env-setup key \
        migrate seed fresh \
        shell tinker optimize clear test queue

# ── Default: tampilkan bantuan ─────────────────────────────────
help: ## 📖 Tampilkan daftar semua perintah
	@printf "\n  \033[1;36m🚀 Sistem Penomoran SK — Docker Commands\033[0m\n"
	@printf "  \033[90m════════════════════════════════════════\033[0m\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}'
	@printf "\n"

# ── Setup Awal ─────────────────────────────────────────────────
env-setup: ## ⚙️  Buat .env dari template Docker
	@if [ ! -f .env ]; then \
		cp .env.docker .env; \
		echo "✅ .env dibuat dari .env.docker"; \
	else \
		echo "⚠️  .env sudah ada. Hapus manual jika ingin reset."; \
	fi

key: ## 🔑 Generate APP_KEY di dalam container
	$(ARTISAN) key:generate

# ── Docker Lifecycle ───────────────────────────────────────────
up: ## 🟢 Jalankan semua container (background)
	$(DOCKER_COMPOSE) up -d
	@echo "✅ App berjalan di http://localhost"

up-dev: ## 🟢 Jalankan termasuk Vite dev server
	$(DOCKER_COMPOSE) --profile dev up -d
	@echo "✅ App: http://localhost | Vite: http://localhost:5173"

down: ## 🔴 Hentikan & hapus semua container
	$(DOCKER_COMPOSE) down

restart: ## 🔄 Restart semua container
	$(DOCKER_COMPOSE) restart

build: ## 🏗️  Build ulang Docker image (dengan cache)
	$(DOCKER_COMPOSE) build

rebuild: ## 🏗️  Build ulang Docker image (TANPA cache)
	$(DOCKER_COMPOSE) build --no-cache

ps: ## 📊 Tampilkan status container
	$(DOCKER_COMPOSE) ps

# ── Logs ───────────────────────────────────────────────────────
logs: ## 📋 Tampilkan semua log (live)
	$(DOCKER_COMPOSE) logs -f

logs-app: ## 📋 Log container app (Laravel/PHP-FPM)
	$(DOCKER_COMPOSE) logs -f app

logs-nginx: ## 📋 Log container Nginx
	$(DOCKER_COMPOSE) logs -f nginx

# ── Database ───────────────────────────────────────────────────
migrate: ## 🗃️  Jalankan migrasi database
	$(ARTISAN) migrate --force

seed: ## 🌱 Seed data awal (Admin & Petugas)
	$(ARTISAN) db:seed --force

fresh: ## ⚠️  Reset database: hapus semua tabel, migrate ulang & seed
	$(ARTISAN) migrate:fresh --seed --force

# ── Laravel Utilities ──────────────────────────────────────────
shell: ## 🐚 Masuk ke terminal container app
	$(DOCKER_COMPOSE) exec app bash

tinker: ## 🔬 Buka Laravel Tinker (REPL)
	$(ARTISAN) tinker

optimize: ## ⚡ Cache config, routes, events & views
	$(ARTISAN) optimize

clear: ## 🧹 Bersihkan semua cache Laravel
	$(ARTISAN) optimize:clear

test: ## 🧪 Jalankan PHPUnit test suite
	$(ARTISAN) test

queue: ## ⚙️  Jalankan queue worker
	$(ARTISAN) queue:work --sleep=3 --tries=3 --max-time=3600
