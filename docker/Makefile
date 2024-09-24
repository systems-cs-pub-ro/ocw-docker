version=stable

IMAGE_NAME = upb-cs-ocw/dokuwiki
IMAGE_FULL = $(IMAGE_NAME):$(version)

BUILD_ARGS = --build-arg VERSION="${version}"
BUILD_ARGS = --label VERSION="${version}"
BUILD_ARGS += --debug 

build:
	docker image build $(BUILD_ARGS) -t $(IMAGE_NAME) -t "$(IMAGE_FULL)" .

run:
	docker run --name ocw-dokuwiki "$(IMAGE_FULL)"

compose:
	docker compose up

composed:
	docker compose up -d

bash:
	docker compose exec -it dokuwiki /bin/bash

clean:
	docker compose down
	docker image rm "$(IMAGE_FULL)"

.PHONY: build run compose composed bash clean

