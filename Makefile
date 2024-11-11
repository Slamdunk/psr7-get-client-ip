ifdef CI
	DOCKER_PHP_EXEC :=
else
	DOCKER_PHP_EXEC := docker compose run --rm php
endif
PHP_BIN=php -d zend.assertions=1

SRCS := $(shell find ./lib ./tests -type f -not -path "*/tmp/*")

all: csfix static-analysis code-coverage
	@echo "Done."

.env: /etc/passwd /etc/group Makefile
	printf "USER_ID=%s\nGROUP_ID=%s\n" `id --user "${USER}"` `id --group "${USER}"` > .env

vendor: .env docker-compose.yml Dockerfile composer.json
	docker compose build --pull
	$(DOCKER_PHP_EXEC) composer update
	$(DOCKER_PHP_EXEC) composer bump
	touch --no-create $@

.PHONY: csfix
csfix: vendor
	$(DOCKER_PHP_EXEC) vendor/bin/php-cs-fixer fix -v $(arg)

.PHONY: static-analysis
static-analysis: vendor
	$(DOCKER_PHP_EXEC) $(PHP_BIN) vendor/bin/phpstan analyse --memory-limit=512M $(PHPSTAN_ARGS)

coverage/ok: vendor $(SRCS) Makefile
	($(DOCKER_PHP_EXEC) $(PHP_BIN) vendor/bin/phpunit $(PHPUNIT_ARGS) \
		&& touch $@)

.PHONY: test
test: coverage/ok

.PHONY: code-coverage
code-coverage: coverage/ok
	$(DOCKER_PHP_EXEC) $(PHP_BIN) \
		vendor/bin/infection \
		--threads=$(shell nproc) \
		--skip-initial-tests \
		--coverage=coverage \
		--show-mutations \
		--verbose \
		--min-msi=100 \
		$(INFECTION_ARGS)
