## Installation

1. Clone the repository
2. Run
    cd "path_to_project"

3. Run
    cp .env.example .env

4. Specify environment variables to connect to DB

5. Run
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"

6. Run
    php composer.phar install

7. Run
    php artisan key:generate

8. Run
    php artisan migrate
    php artisan db:seed

## Tests

1. Run
    cp .env.example .env.testing
2. Run
    php artisan key:generate --env=testing
3. Specify environment variables to connect to DB
4. Run
    ./vendor/bin/phpunit

## API

1. Run
    php artisan serve

### Endpoints:
create order:
    POST /api/orders

update order, statuses: 'new', 'processed', 'transferred', 'completed', 'canceled'
    PUT  /api/orders

JSON data example:
    {
    	"status": "canceled"
    }
or
    {
        "product_ids": [1, 2]
    }

## Console commands

1. php artisan order:create
2. php artisan order:update {orderId} {--status=} {--product_id=*}

Example:
    php artisan order:create
    php artisan order:update 1 --product_id=2 --product_id=3
    php artisan order:update 1 --status=processed