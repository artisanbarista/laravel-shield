# Block bad bots and IPs that visit exploit URLs

Your application is hammered by malicious bots and visitors that try out exploit URLs. This package detects those and blocks their IP addresses. Blocked users are denied access to your application until their block expires.

1. Block exploit URLs like `/wp-admin` and `?invokefunction&function=call_user_func_array&vars[0]=phpinfo`.
2. Block user Agents like `Seznam`, `Flexbot` and `Mail.ru`.
3. Set the expiration time for IP blocks.

## Installation

Step 1: Install the package via composer:

```bash
composer require webdevartisan/laravel-shield
```

Step 2: Make sure to register the Middleware.

To use it on all requests, add it as the first option to the `web` section under `$middlewareGroups` in file app/Http/Kernel.php.

```php
protected $middlewareGroups = [
    'web' => [
        \Webdevartisan\LaravelShield\Http\Middleware\BlockMaliciousUsers::class,
    ],
];
```

To use it on specific requests, add it to any group or to the `protected $middleware` property in file app/Http/Kernel.php.

```php
protected $middleware = [
        \Webdevartisan\LaravelShield\Http\Middleware\BlockMaliciousUsers::class,
    ];
```

Step 3: Optionally publish the config file with:

```
php artisan vendor:publish --provider="Webdevartisan\LaravelShield\LaravelShieldServiceProvider" --tag="config"
```

## Usage

The package uses auto discover. The package uses a middleware class that does the checking and blocking.

## Config settings

### Enabling checking

You can enable or disable URL checking and User Agent checking in the published config file, or by setting these values in .env (enabled by default).

```apacheconf
URL_DETECTION_ENABLED=true
USER_AGENT_DETECTION_ENABLED=true
```

### Expiration time

Set the block expiration time (in seconds) in the published config file, or by setting this value in .env (3600 seconds by default).

```apacheconf
AI_BLOCKER_EXPIRATION_TIME=3600
```

### Define malicious URLs

Define malicious URLs in the published config file, or by setting this value in .env, separated by a pipe. You need only use part of the malicious string. Matching is case insensitive.

Example: setting `wp-admin` will block both '/wp-admin', '/index.php/wp-admin/foo' and '/?p=wp-admin'.

Defaults: `call_user_func_array|invokefunction|wp-admin|wp-login|.git|.env|install.php|/vendor`

```apacheconf
AI_BLOCKER_MALICIOUS_URLS='call_user_func_array|invokefunction|wp-admin|wp-login|.git|.env|install.php|/vendor'
```

### Define malicious User Agents

Define malicious User Agents in the published config file, or by setting this value in .env, separated by a pipe. You need only use part of the malicious string. Matching is case insensitive.

Example: setting `seznam` will block User Agent 'Mozilla/5.0 (compatible; SeznamBot/3.2-test4; +http://napoveda.seznam.cz/en/seznambot-intro/)'.

```apacheconf
AI_BLOCKER_MALICIOUS_USER_AGENTS='dotbot|linguee|seznam|mail.ru'
```

### Define storage class implementation

By default, blocked IPs are stored in cache, using storage implementation `\Webdevartisan\LaravelShield\Services\BlockedIpStoreCache::class`.

You can set the storage class you wish to use in the published config file, or by setting this value in .env. You can choose from:
- \Webdevartisan\LaravelShield\Services\BlockedIpStoreCache
- \Webdevartisan\LaravelShield\Services\BlockedIpStoreDatabase


```apacheconf
AI_BLOCKER_STORAGE_IMPLEMENTATION_CLASS='\Webdevartisan\LaravelShield\Services\BlockedIpStoreCache'
```

### Testing

```bash
composer test
XDEBUG_MODE=coverage vendor/bin/phpunit tests --coverage-html code-coverage 
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email webdevartisan@mail.ru instead of using the issue tracker.

## Credits

-   [WebDevArtisan](https://github.com/webdevartisan)
-   [Joost van Veen](https://github.com/accentinteractive)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
