# Block bad bots and IPs that visit exploit URLs

Your application is hammered by malicious requests that try out exploit URLs. This package detects those and blocks their IP addresses. Blocked users are denied access to your application until their block expires.

1. Block exploit URLs like `/wp-admin` and `?invokefunction&function=call_user_func_array&vars[0]=phpinfo`.
2. Block user Agents like `Seznam`, `Flexbot` and `Mail.ru`.
3. Set the expiration time for IP blocks.
4. Set IP whitelist/blacklist.

## Installation

Step 1: Install the package via composer:

```bash
composer require artisanbarista/laravel-shield
```

Step 2: Make sure to register the Middleware.

To use it on all requests, add it as the first option to the `web` section under `$middlewareGroups` in file app/Http/Kernel.php.

```php
protected $middlewareGroups = [
    'web' => [
        \Artisanbarista\LaravelShield\Http\Middleware\BlockMaliciousUsers::class,
    ],
];
```

To use it on specific requests, add it to any group or to the `protected $middleware` property in file app/Http/Kernel.php.

```php
protected $middleware = [
        \Artisanbarista\LaravelShield\Http\Middleware\BlockMaliciousUsers::class,
    ];
```

Step 3: Optionally publish the config file with:

```
php artisan vendor:publish --provider="Artisanbarista\LaravelShield\LaravelShieldServiceProvider" --tag="config"
```

## Usage

The package uses auto discover. The package uses a middleware class that does the checking and blocking.

## Config settings

### Enabling shield

You can enable or disable the shield in the published config file, or by setting the value in .env (enabled by default).

```apacheconf
SHIELD_PROTECTION_ENABLED=true
```

### Expiration time

Set the block expiration time (in seconds) in the published config file, or by setting this value in .env (3600 seconds by default).

```apacheconf
SHIELD_EXPIRATION_TIME=3600
```

### Maximum Attempts
Set the maximum allowed number of malicious requests, before blocking the IP. Default is 5. You can change it in the config or the .env.
```apacheconf
SHIELD_MAX_ATTEMPTS=5
```

### Define malicious URLs

Define malicious URLs in the published config file. You need only use part of the malicious string. Matching is case insensitive.

Example: setting `wp-admin` will block both '/wp-admin', '/index.php/wp-admin/foo' and '/?p=wp-admin'.


### Define malicious User Agents

Define malicious User Agents in the published config file.

Example: setting `seznam` will block User Agent 'Mozilla/5.0 (compatible; SeznamBot/3.2-test4; +http://napoveda.seznam.cz/en/seznambot-intro/)'.


### Define storage class implementation

By default, blocked IPs are stored in cache, using storage implementation `\Artisanbarista\LaravelShield\Services\BlockedIpStoreRateLimiter::class`.

You can create a different storage class you wish to use, and replace it in the config file, or by setting this value in .env:
- \Artisanbarista\LaravelShield\Services\BlockedIpStoreRateLimiter


```apacheconf
SHIELD_STORAGE_IMPLEMENTATION_CLASS='\Artisanbarista\LaravelShield\Services\BlockedIpStoreRateLimiter'
```

### Testing

```bash
composer test
XDEBUG_MODE=coverage vendor/bin/phpunit tests --coverage-html code-coverage 
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Credits

-   [ArtisanBarista](https://github.com/artisanbarista)
-   [Joost van Veen](https://github.com/accentinteractive)
-   [accentinteractive](https://github.com/accentinteractive)
-   [webdevartisan](https://github.com/webdevartisan)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
