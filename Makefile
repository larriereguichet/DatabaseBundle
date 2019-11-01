.PHONY: install tests install@php-cs-fixer phpunit@run

current_dir = $(shell pwd)

install: composer.lock install@php-cs-fixer

composer.lock:
	composer install

tests:  phpunit@run php-cs-fixer@fix

phpunit@run:
	bin/simple-phpunit
	@echo "Results file generated file://$(current_dir)/var/phpunit/coverage/index.html"

php-cs-fixer@fix:
	bin/php-cs-fixer fix

php-cs-fixer@install:
	wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O bin/php-cs-fixer
	chmod +x bin/php-cs-fixer
