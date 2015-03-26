# Thrift tutorial with PHP/JS application

## Requirements
You should install following software:
- php 5.5+ (with mysqlnd)
- composer
- VirtualBox
- Vagrant

## How to run?
```
vagrant up
composer install -o
vendor/bin/doctrine orm:schema-tool:create
php -S localhost:9080 -t web
```

Open [the link](http://localhost:9080/index.html) in Chrome/Safari.