# PHP API client for Redmine

PHP API client for [Redmine](http://redmine.org).

## Installation

Be sure to provide implementations for `psr/http-client-implementation` and `psr/http-factory-implementation`. For example:
```
composer require nyholm/psr7 guzzlehttp/guzzle
```

Use composer to install the library:
```
composer require muxx/redmine-api-client-php
```

## Symfony

Enable [PSR-18 in HttpClient](https://symfony.com/doc/current/http_client.html#psr-18-and-psr-17)

```yaml
services:
    Redmine\ApiClient:
        arguments:
            $url: 'https://redmine.somehost.com'
            $apiKey: 'some-api-key'
```

## Usage

```php
require 'vendor/autoload.php';

$c = new Redmine\ApiClient(
    new HttpClient(),
    new Psr17Factory(),
    new Psr17Factory(),
    'https://redmine.somehost.com',
    'some-api-key'
);

$response = $c->requestGet('projects/some-project/memberships');

foreach ($response['memberships'] as $membership) {
    if (isset($membership['user']['name'])) {
        echo sprintf("User: %s\n", $membership['user']['name']);
    }
    if (isset($membership['group']['name'])) {
        echo sprintf("Group: %s\n", $membership['group']['name']);
    }
}
```
