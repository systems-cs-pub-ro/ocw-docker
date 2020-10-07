version=0.1

build:
	docker image build -t upb-cs-ocw/dokuwiki:${version} --build-arg VERSION=${version} .

run:
	docker run -d --name ocw-dokuwiki upb-cs-ocw/dokuwiki:${version}

run-web:
	docker network create --driver bridge --label project=dokuwiki --attachable --scope local dokuwiki-network
	docker run -d -p 80:80 --name ocw-dokuwiki --network dokuwiki-network -v `pwd`/../courses/:/var/www/html/courses/ upb-cs-ocw/dokuwiki:${version}

bash:
	docker exec -it ocw-dokuwiki /bin/bash

clean:
	docker rm -f ocw-dokuwiki
	docker image rm upb-cs-ocw/dokuwiki:${version}
