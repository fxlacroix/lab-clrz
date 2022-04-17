# {json} splitter to {csv} 

## todo
1. better way to browse object mapping relationship
2. Isolate features of the SplitterService so that it could be easier testable
3. Better check parameters in SplitterService
4. use Elastic Search in case of large volume, to create a denormalized model with all power in a document ?

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker-compose build --pull --no-cache` to build fresh images
3. Run `docker-compose up` (the logs will be displayed in the current shell)
4. Run `docker-compose down --remove-orphans` to stop the Docker containers.
5. Connect to the docker `php docker exec -it caddy-php-docker_php_1 sh `
6. Once in the project just install the vendor, Running `composer install`
7. you can know run the command `php bin/console app:split --file=files/input/sample_data_test.json --mapping=teams --output=csv`
8. the result should be in `files/output/{format}`, here it is csv

## Features

* Production, development and CI ready
* Automatic HTTPS (in dev and in prod!)
* HTTP/2, HTTP/3 and [Preload](https://symfony.com/doc/current/web_link.html) support
* Built-in [Mercure](https://symfony.com/doc/current/mercure.html) hub
* [Vulcain](https://vulcain.rocks) support
* Just 2 services (PHP FPM and Caddy server)
* Super-readable configuration
