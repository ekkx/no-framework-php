# Common
.PHONY: setup setup-backend

DOCKER_COMMAND = docker compose run --rm app bash -c

setup:
	docker compose build app
	make setup-backend
	make migrate
	@echo "please use 'make up'"

setup-backend:
	$(DOCKER_COMMAND) "php -r \"file_exists('.env') || copy('.env.example', '.env');\""
	$(DOCKER_COMMAND) "composer install"

# Database
.PHONY: migrate reset

migrate:
	$(DOCKER_COMMAND) "cd database && ./migrate.sh"

reset:
	$(DOCKER_COMMAND) "cd database && rm -f database.sqlite"

# Docker
.PHONY: up down log ps shell

up:
	docker compose up -d

down:
	docker compose down

log:
	docker compose logs -f

ps:
	docker compose ps

shell:
	docker compose exec -it app bash
