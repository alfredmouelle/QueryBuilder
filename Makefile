.PHONY: install
install: composer.json
	composer install

.PHONY: server
server: install
	php -S localhost:8000

.PHONY: test
test: install
	./vendor/phpunit/phpunit/phpunit ./tests/QueryBuilderTest.php