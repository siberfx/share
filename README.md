# Share

[![Latest Version on Packagist](https://img.shields.io/packagist/v/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)
[![Total Downloads](https://img.shields.io/packagist/dt/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)
[![Tests](https://img.shields.io/github/actions/workflow/status/siberfx/share/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/siberfx/share/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/badge/php-8.4%20%7C%208.5-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/supported-versions.php)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x%20%7C%2013.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)

Generate social share links with Laravel 12 & 13 (PHP 8.4 and 8.5).

This is a fork of John's share for Laravel 4.

## Services available

| Service      | Method         |
|--------------|----------------|
| Bluesky      | `bluesky`      |
| Delicious    | `delicious`    |
| Digg         | `digg`         |
| Email        | `email`        |
| Evernote     | `evernote`     |
| Facebook     | `facebook`     |
| Gmail        | `gmail`        |
| LinkedIn     | `linkedin`     |
| Pinterest    | `pinterest`    |
| Reddit       | `reddit`       |
| Telegram     | `telegramMe`   |
| Threads      | `threads`      |
| Tumblr       | `tumblr`       |
| Twitter      | `twitter`      |
| vk.com       | `vk`           |
| WhatsApp     | `whatsapp`     |
| X            | `x`            |

## Requirements

- PHP 8.4 or 8.5
- Laravel 12.x or 13.x

## Installation

Install the Composer dependency:

```bash
composer require siberfx/share
```

The service provider (`Siberfx\Share\ShareServiceProvider`) and the `Share`
facade (`Siberfx\Share\Facade\Share`) are auto-registered via Laravel package
discovery — no manual registration required.

## Usage

Get a link (example with X):

```php
use Siberfx\Share\Facade\Share;

Route::get('/', function () {
    return Share::load('http://www.example.com', 'My example')->x();
});
```

Returns a string:

```
https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&text=My%20example
```

`load()` takes the URL, an optional title and an optional media URL (used by
services such as Pinterest and vk):

```php
Share::load('http://www.example.com', 'My example', 'http://www.example.com/image.jpg')->pinterest();
```

Get many links:

```php
Share::load('http://www.example.com', 'Link description')->services('facebook', 'linkedin', 'x');

// or with an array
Share::load('http://www.example.com', 'Link description')->services(['facebook', 'linkedin', 'x']);
```

Returns an array:

```json
{
    "facebook": "https://facebook.com/sharer/sharer.php?u=http%3A%2F%2Fwww.example.com&title=Link%20description",
    "linkedin": "https://linkedin.com/shareArticle?url=http%3A%2F%2Fwww.example.com&title=Link%20description&mini=true",
    "x": "https://x.com/intent/post?url=http%3A%2F%2Fwww.example.com&text=Link%20description"
}
```

Pass `true` as the last argument to get an object instead of an array:

```php
$links = Share::load('http://www.example.com', 'Link description')->services('facebook', 'x', true);

$links->facebook;
```

Get ALL the links (every service in the config):

```php
Share::load('http://www.example.com', 'Link description')->services();
```

Check whether a service exists before calling it — calling an undefined service
throws a `BadMethodCallException`:

```php
if (Share::has('mastodon')) {
    // ...
}
```

In Blade:

```blade
<a href="{{ Share::load(url()->current(), $post->title)->x() }}" target="_blank" rel="noopener">Share on X</a>
```

## Customization

Publish the package config (and, optionally, the views):

```bash
php artisan vendor:publish --tag=social-share-config
php artisan vendor:publish --tag=social-share-views
```

### Query-string services

Most services only need a definition in `config/social-share.php`:

```php
'mynewservice' => [
    'uri' => 'https://mynewservice.example.com/share', // may already contain a query string
    'urlName' => 'u',          // parameter for the URL   (default: url)
    'titleName' => 'text',     // parameter for the title (default: title)
    'mediaName' => 'image',    // parameter for the media (omitted when not set)
    'extra' => ['via' => 'me'], // fixed extra parameters
    'only' => ['url', 'title'], // limit which values are sent (default: url, title, media)
],
```

### View-based services

For anything more custom, point the service at a Blade view:

```php
'mynewservice' => ['view' => 'share.mynewservice'],
```

The view has access to:

- `$service` - the service definition (shown above).
- `$sep` - separator used between parameters, defaults to `&`. Configurable as `social-share.separator`.
- `$url` - the URL being shared.
- `$title` - the title being shared.
- `$media` - media link being shared.

Example:

```blade
https://mynewservice.example.com?url={{ rawurlencode($url) }}<?php echo $sep; ?>title={{ rawurlencode("Check this out! $title. See it here: $url") }}
```

Another example for the `email` service. Change the service config to be `['view' => 'whatever']` and put this in the view file:

```blade
mailto:?subject={{ rawurlencode("Wow, check this: $title") }}<?php echo $sep; ?>body={{ rawurlencode("Check this out! $title. See it here: $url") }}
```

Localizing? Easy, use Laravel's `trans()` helper:

```blade
mailto:?subject={{ rawurlencode(trans('share.email-subject', compact('url', 'title', 'media'))) }}<?php echo $sep ?>body={{ rawurlencode(trans('share.email-body', compact('url', 'title', 'media'))) }}
```

Create a file at `lang/en/share.php` with your choice of subject and body. URLs arguably have a maximum length of 2000 characters.

Notice the use of `<?php echo $sep; ?>`. It's the only way to print out an unencoded ampersand (if configured that way).

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

The MIT License (MIT).
