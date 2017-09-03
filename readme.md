# Bookshelf app

## Installation

The application based on **Lumen** framework by **Laravel**, so you can use an official, pre-packaged Vagrant box [**Laravel Homestead**](https://laravel.com/docs/5.4/homestead) for deploying.

In other way:

- clone this repo to a some directory
```bash
$ git clone https://github.com//mixailshaulsky/bookshelf.git bookshelf-app
```

- create **.env**  file.
```bash
$ cd bookshelf-app
$ cp .env.example .env
```
and fill the database options (starts with DB_*)

-  install dependencies with composer
```bash
$ composer install
```

- migrate database tables
```bash
$ php artisan migrate
```

- start the local php server (or configure apache/nginx virtual host if prefer) with **public directory**  as document root
```bash
$ php -S localhost:8000 -t public
```

- ...

- :thumbsup: profit!

## Client-side application

Based on AngularJS 1.6.6 framework

Default location - /public/app/

Can be used as standalone app. In this case api URL must be specified in app/app.config.js (apiUrl constant).

## API Documentation

Swagger documentation available on http://your-app-url/documentation/

## License

Licensed under the [MIT license](http://opensource.org/licenses/MIT)
