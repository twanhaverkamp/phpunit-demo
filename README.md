# Landing page skeleton
This project contains a lightweight Symfony 5 installation with only the bear minimum amount of components
to function as a skeleton for new landing pages. Once you forked this repository
it's up to you to modify it to your own needs.

## Getting started
### Installation
This project runs on [Docker Compose] and therefore requires that you have [Docker Desktop] installed.
Once you have done that, run the following command in your terminal:

```
$ docker-compose up -d
```

Thanks to [Traefik] you can now access the project with this URL:
http://my-landing-page.localhost

> *Note: The URL can be changed by editing the `PROJECT_DOMAIN` environment variable.*

## Composer
To install [Composer] packages you can run the following command:

```
$ docker-compose run composer require ...
```

## PHPUnit
This project comes with [PHPUnit], where functional and unit tests are organized in two separate directories.
To execute all tests, run the following command:

```
$ docker-compose run php ./vendor/bin/phpunit
```

## PHPStan
The latest [PHPStan] version is installed and comes with a default configuration file which can be overridden
by creating your own `phpstan.neon` file. You can run an analysis with the following command:

```
$ docker-compose run php ./vendor/bin/phpstan analyse
```

## Xdebug
The latest [Xdebug] version is installed and enabled by default. You can modify the settings by changing
the `XDEBUG_MODE` and `XDEBUG_CONFIG` environment variable values.

[Composer]: https://getcomposer.org/doc/00-intro.md
[Docker Compose]: https://docs.docker.com/compose/
[Docker Desktop]: https://www.docker.com/products/docker-desktop
[PHPStan]: https://phpstan.org/user-guide/getting-started
[PHPUnit]: https://phpunit.readthedocs.io/en/9.5/
[Traefik]: https://doc.traefik.io/traefik/
[Xdebug]: https://xdebug.org/docs/
