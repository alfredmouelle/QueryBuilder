.PHONY: install
install: composer.json
	composer install

.PHONY: test
test: install
	./vendor/phpunit/phpunit/phpunit ./tests/QueryBuilderTest.php