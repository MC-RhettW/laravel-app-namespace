## Laravel App Namespace Command

Provides forward support for the Laravel 5.8 `app:name` command in Laravel 6 and Laravel 7 applications. Based on [Laravel App](https://github.com/andrey-helldar/laravel-app) by Andrey Helldar.

### Installation
Add the private repository to the `repositories` block of your Laravel project's composer.json file:

```json
{
  "repositories": [{
      "name":   "mcdev/laravel-app-namespace",
      "type":   "vcs",
      "url":    "https://github.com/MC-RhettW/laravel-app-namespace.git"
    }]
}
```

Then add the package to the `require-dev` block:

```json
{
  "require-dev": {
    "mcdev/laravel-app-namespace": "*"
  }
}
```

You may also use composer to update your dev requirements block:

```
composer require mcdev/laravel-appnamespace --dev
```

### Usage

Set the application namespace by console command:

```
php artisan app:name <name>
```
