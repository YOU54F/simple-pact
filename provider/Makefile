build:
	@docker build -t acmachado/pact-provider:1.0 .
.PHONY: build

run-it:
	@docker run -it --rm --name provider --network contract-pact_default acmachado/pact-provider:1.0 php -S provider:8000

start_and_test:
	{ php -S localhost:8000 & }; \
	pid=$$!; \
	vendor/bin/phpunit; \
	r=$$?; \
	kill $$pid; \
	exit $$r

.PHONY: run-it

