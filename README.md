# Run Patches Command Once

執行一次性的補丁指令

## Installation

You can install the package via composer:

```bash
composer require vswteam/laravel-patches-command
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Vswteam\LaravelPatchesCommand\LaravelPatchesCommandServiceProvider" --tag="migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Vswteam\LaravelPatchesCommand\LaravelPatchesCommandServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
return [
    'patches' => [
    ]
];
```

## Usage

修改 `laravel-patches-command.php`，加入要執行的指令列表

```php
return [
    'patches' => [
        'patch:clear-dummy-logs' => '2022-01-01 Clear Dummy Logs',
        'patch:clear-dummy-files' => '2022-01-02 Clear Dummy Files',
    ]
];
```

然後執行 `php artisan patches:run` 即可

## Testing

``` bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
