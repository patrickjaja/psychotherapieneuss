.PHONY: help build up down restart logs shell mysql setup clean

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker containers
	docker compose build --no-cache

up: ## Start Docker containers
	docker compose up -d

down: ## Stop Docker containers
	docker compose down

restart: ## Restart Docker containers
	docker compose restart

logs: ## Show container logs
	docker compose logs -f

logs-php: ## Show PHP container logs
	docker compose logs -f php

logs-mysql: ## Show MySQL container logs
	docker compose logs -f mysql

shell: ## Open shell in PHP container
	docker compose exec php bash

mysql: ## Open MySQL CLI
	docker compose exec mysql mysql -u psychotherapieneuss -ppsychotherapieneuss psychotherapieneuss

setup: up ## Setup database tables
	docker compose exec php php setup.php

clean: ## Remove containers and volumes
	docker compose down -v --remove-orphans

status: ## Show container status
	docker compose ps

rebuild: down build up ## Full rebuild
