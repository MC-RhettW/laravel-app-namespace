## Laravel App Namespace Command

Provides forward support for the Laravel 5.8 `app:name` command in Laravel 6 and Laravel 7 applications. Based on [Laravel App](https://github.com/andrey-helldar/laravel-app) by Andrey Helldar.

### Installation
Install from the command line using composer CLI:
```
composer require andrey-helldar/laravel-app --dev
```

Or manually update your require block and run your project's update and build routines:

```json
{
    "require-dev": {
        "andrey-helldar/laravel-app": "^1.0"
    }
}
```


### Using

Set the application namespace by console command:

```
php artisan app:name <name>
```
