# PHPUnit demo
A simple demo project for explaining the basics of software testing and how to use PHPUnit.

## Getting started
### Installation
This project runs on [Docker Compose] and therefore requires that you have [Docker Desktop] installed.
Once you have done that, run the following command in your terminal:

```
$ docker-compose up -d
```

Thanks to [Traefik] you can now access the project with this URL:
http://phpunit-demo.localhost

## PHPUnit
This project comes with [PHPUnit], where functional-, integration- and unit tests are organized in three separate
directories. To execute all tests, run the following command:

```
$ docker-compose run php ./vendor/bin/phpunit
```

> *Note: Pass the `--coverage-text=<file>` option to generate a code-coverage report.*

[Docker Compose]: https://docs.docker.com/compose/
[Docker Desktop]: https://www.docker.com/products/docker-desktop
[PHPUnit]: https://phpunit.readthedocs.io/en/9.5/
[Traefik]: https://doc.traefik.io/traefik/
