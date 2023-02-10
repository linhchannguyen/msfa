#### Install full version of all packages

```
composer update
```

### If there is a request to upgrade the version then run this command

`composer install --ignore-platform-reqs`

### Disable TELESCOPE

`edit file env property TELESCOPE_ENABLED to false`

### Swagger

-   Guide: L5-Swagger in Laravel REST APIs
    https://viblo.asia/p/l5-swagger-in-laravel-rest-apis-m68Z0x1AZkG

-   Run Swagger
    `php artisan l5-swagger:generate`

-   If you don't want to have to run the above command every time you change the api documentation, you'll need to run the above command in the .env file
    `L5_SWAGGER_GENERATE_ALWAYS=true`

-   Run Execute. Please set path in swagger. Example:
    `path="/api/widget/message"`
