Laravel 5.7 API using JSON web tokens (JWTs). The client is Vue 2.5

uses tymons jwt-auth on the PHP to generate a JWT which stored on the client side using websanova.
The Vue components are tested with Jest, the API with PHPUnit.

Installation:

composer install
npm install

## **Configure MySQL connection details in .env**

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={database name}
DB_USERNAME={database user}
DB_PASSWORD={database password}


## **Configure Keys**

php artisan key:secret (Laravel key)
php artisan jwt:secret (Tymon JWT key)

## Init the application

Init the application in a either Homestead (Mac) or Laragon (windows)

## Feature testing

phpunit
