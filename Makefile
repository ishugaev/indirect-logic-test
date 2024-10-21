# Executables (local)
DOCKER_COMP = docker compose

# Additional environment variables
PROJECT_NAME=$(shell grep PROJECT_NAME .env.*.local | cut -d '=' -f 2)
XDEBUG_MODE=$(shell grep XDEBUG_MODE .env.*.local | cut -d '=' -f 2)

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
CRON_CONT = $(DOCKER_COMP) exec cron
WORKER_CONT = $(DOCKER_COMP) exec worker

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help init-dev build up start down logs sh composer vendor sf cc test wbash wup wdown wlogs cbash cup cdown clogs

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
init-dev: ## Initialize the development environment by copying the .env.dev.dist file to .env.dev.local
	@if [ ! -f .env.dev.local ]; then cp .env.dev.dist .env.dev.local; fi

build: ## Builds the Docker images with the github token and the project name
	@GITHUB_TOKEN=$(GITHUB_TOKEN) IMAGES_PREFIX=$(PROJECT_NAME) $(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@XDEBUG_MODE=$(XDEBUG_MODE) IMAGES_PREFIX=$(PROJECT_NAME) $(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

## —— Cron commands 🕰️ ————————————————————————————————————————————————————————————————
cup: ## Start the docker hub in detached mode (no logs)
	@XDEBUG_MODE=$(XDEBUG_MODE) IMAGES_PREFIX=$(PROJECT_NAME) $(DOCKER_COMP) --profile cron up --detach

cdown: ## Stop the docker hub
	@$(DOCKER_COMP) --profile cron down --remove-orphans

clogs: ## Show live logs
	@$(CRON_CONT) logs --tail=0 --follow

cbash: ## Connect to the cron container via bash so up and down arrows go to previous commands
	@$(CRON_CONT) bash

## —— Worker commands 👷 ————————————————————————————————————————————————————————————————
wup: ## Start the docker hub in detached mode (no logs)
	@XDEBUG_MODE=$(XDEBUG_MODE) IMAGES_PREFIX=$(PROJECT_NAME) $(DOCKER_COMP) --profile worker up --detach

wdown: ## Stop the docker hub
	@$(DOCKER_COMP) --profile worker down --remove-orphans

wlogs: ## Show live logs
	@$(WORKER_CONT) logs --tail=0 --follow

wbash: ## Connect to the worker container via bash so up and down arrows go to previous commands
	@$(WORKER_CONT) bash

## —— General commands 🧙 ————————————————————————————————————————————————————————————————

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

lint: ## Start linters
	@$(DOCKER_COMP) exec php php -l src/ && bin/phpcs

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-progress --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf
