# Dockable

Dockable is a small PHP framework for working with HTTP requests and responses. The name comes from my desire to gain a better understanding of using [Docker](https://www.docker.com/).

Much of what is developed here can already be found in Laravel or Lumen. This project was built out of a desire to learn how one might create an API application from the ground-up.

## Environment

Docker is used to manage the webserver, PHP, and database images. The images run with custom commands are setup inside the `docker/` directory. The following images are being used for now:

- nginx:1.13.8
- php:7-fpm
- mongo:3.7.1-jessie

Environment settings are read using the Dotenv convention. All possible options are listed in `.env.example`. To change these settings, be sure to copy them over to a proper `.env` file:

```bash
cp .env.example .env
```

## Database

The intention is to have a properly developed, DBMS-agnostic approach. However, the framework currently only has a MongoDB adapter.

## License

The Dockable framework is open-sourced software licensed under the [MIT license](https://github.com/rideron89/dockable/blob/master/COPYRIGHT).
