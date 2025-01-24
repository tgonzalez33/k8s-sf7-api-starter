#!make
.DEFAULT_GOAL= help

help: 
	@echo "\033[33mUsage:\033[0m\n  make [target] [arg=\"val\"...]\n\n\033[33mTargets:\033[0m"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' Makefile| sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

start:
	docker compose build && docker compose -f docker-compose.yml up -d --remove-orphans

stop:
	docker compose -f docker-compose.yml stop

down:
	docker compose -f docker-compose.yml down