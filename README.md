# Containerized DokuWiki for ACS@UPB Open CourseWare

This repository contains Docker scripts for hosting the
[DokuWiki](https://www.dokuwiki.org/)-based Open CourseWare instance of the
Computer Science department, POLITEHNICA University of Bucharest.

Requirements:

- [Docker](https://docker.com/) & [Docker Compose](https://docs.docker.com/compose/);
- [GNU Make](https://www.gnu.org/software/make/) + Linux utils (for development);

Features:

- based on the official [DokuWiki Docker image](https://github.com/dokuwiki/docker), with some customizations;
- integrates [DokuWiki Farmer plugin](https://www.dokuwiki.org/plugin:farmer) for facilitating a decentralized management of various courses / groups;
- various wiki content plugins & management scripts;
- OAuth integration for our University's Single Sign-On service -- TODO;

Note: this repository DOES NOT CONTAIN the wiki data (and associated metadata),
which is usually mounted as external container volume, enforcing strict separation
of concerns (plus security & privacy!).

## Usage

First, clone this repo in your preferred location:

```sh
git clone "https://github.com/systems-cs-pub-ro/ocw-docker.git" ocw-docker
```

Makefile + compose file are available for easing the development lifecycle:

```sh
# use DEV=1 to preload it with a demo!
make build compose DEV=1
```

This will use the gitignored `./tmp/` directory for storing the dokuwiki instance
data (create `.env` to customize it).
See [docker-compose.dev.yml](./docker-compose.dev.yml) for more details.
