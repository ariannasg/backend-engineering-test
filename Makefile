.PHONY: test shell

resolve-dependencies:
	docker-compose run --rm shell composer update

clear-cache-dev:
	docker-compose run --rm shell php bin/console cache:clear

clear-cache-test:
	docker-compose run --rm shell php bin/console cache:clear --env=test

list-autowiring:
	docker-compose run --rm shell php bin/console debug:autowiring

analyse-file-one:
	docker-compose run --rm shell php bin/console app:analyse-metrics

analyse-file-two:
	docker-compose run --rm shell php bin/console app:analyse-metrics --path-to-file="resources/fixtures/2.json"

analyse-file-one-in-bytes:
	docker-compose run --rm shell php bin/console app:analyse-metrics --output-unit-symbol="B"

analyse-file-two-in-bytes:
	docker-compose run --rm shell php bin/console app:analyse-metrics --path-to-file="resources/fixtures/2.json" --output-unit-symbol="B"

test:
	docker-compose run --rm test

shell:
	docker-compose run --rm shell