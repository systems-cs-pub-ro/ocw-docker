version=stable

IMAGE_NAME = upb-cs-ocw/dokuwiki
IMAGE_FULL = $(IMAGE_NAME):$(version)

BUILD_ARGS = --build-arg VERSION="${version}"
BUILD_ARGS = --label VERSION="${version}"
#BUILD_ARGS += --debug 

ifneq ($(wildcard docker-compose.local.yml),) 
	COMPOSE_FILE ?= docker-compose.local.yml
endif
COMPOSE_FILE ?= docker-compose.yml

build:
	docker image build $(BUILD_ARGS) -t $(IMAGE_NAME) -t "$(IMAGE_FULL)" .

run:
	docker run --name ocw-dokuwiki "$(IMAGE_FULL)"

compose:
	docker compose -f $(COMPOSE_FILE) up

composed:
	docker compose -f $(COMPOSE_FILE) up -d

bash:
	docker compose -f $(COMPOSE_FILE) exec -it dokuwiki /bin/bash

clean:
	docker compose -f $(COMPOSE_FILE) down
	docker image rm "$(IMAGE_FULL)"

.PHONY: build run compose composed bash clean

