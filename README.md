# test-recrutement-laravel

Mini backoffice to test applicants tech level with Laravel

# Prerequisites:

* PHP
* Composer

# Installation:

* Environment

Copy .env.default to .env

* Migrations

docker-compose exec app composer update

docker-compose exec app php artisan migrate

* Running the queue

docker compose exec app php artisan queue:work rabbitmq --queue=maintenances

* Running tests

docker compose exec app php artisan test

# Start

http://localhost:14200/

