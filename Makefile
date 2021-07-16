SHELL=/bin/bash

.PHONY: help
.PHONY: build
.PHONY: run
.PHONY: check
.PHONY: test
.PHONY: coverage

help:
	@echo "make build	Builds the project from scratch"
	@echo "make run	Runs the project"
	@echo "make check	Checks the project for coding standards"
	@echo "make test	Executes PHPUnit tests"
	@echo "make coverage	Executes PHPUnit tests with code coverage"

build:
	composer install
	./bin/console doctrine:database:drop --force --quiet || true
	./bin/console doctrine:database:create
	./bin/console doctrine:schema:create
	./bin/console doctrine:fixtures:load --group=prod -n

run:
	symfony serve

check:
	./vendor/bin/php-cs-fixer fix

test:
	./bin/console doctrine:fixtures:load -n
	./bin/phpunit

coverage:
	./bin/console doctrine:fixtures:load -n
	XDEBUG_MODE=coverage ./bin/phpunit --coverage-html=var/coverage
