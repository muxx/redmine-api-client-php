# PHP API client for Redmine

PHP API client for [Redmine](http://redmine.org).

## Installation

Use composer to install the library:
```
composer require muxx/redmine-api-client-php
```

## Usage

```php
require 'vendor/autoload.php';

$c = new Redmine\ApiClient('https://redmine.somehost.com', 'some-api-key');

$response = $c->requestGet('projects/consultant/memberships');

foreach ($response['memberships'] as $membership) {
    if (isset($membership['user']['name'])) {
        echo sprintf("User: %s\n", $membership['user']['name']);
    }
    if (isset($membership['group']['name'])) {
        echo sprintf("Group: %s\n", $membership['group']['name']);
    }
}
```
