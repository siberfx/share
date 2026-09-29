# Laravel Share

[![Latest Version on Packagist](https://img.shields.io/packagist/v/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)
[![Total Downloads](https://img.shields.io/packagist/dt/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)
[![Tests](https://img.shields.io/github/actions/workflow/status/siberfx/share/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/siberfx/share/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/badge/php-8.4%20%7C%208.5-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/supported-versions.php)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x%20%7C%2013.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/siberfx/share.svg?style=flat-square)](https://packagist.org/packages/siberfx/share)

Social share links and ready-made share buttons for Laravel.

`siberfx/share` generates correctly encoded share URLs for 17 platforms. It can
also render accessible share buttons styled for **Bootstrap**, **Tailwind CSS**
or plain HTML, with icons from **Font Awesome**, **Line Awesome** or
**Bootstrap Icons**.

## Features

- Share URLs for X, Facebook, LinkedIn, WhatsApp, Bluesky, Threads, Reddit, Telegram, email and more
- Ready-made share buttons as a Blade component or a single PHP call
- Three themes (Bootstrap 5, Tailwind CSS, plain HTML) that work with any of the three icon sets
- Icon stylesheets loaded from a CDN, pinned to a fixed version and protected by Subresource Integrity
- Accessible by default: `aria-label`s, `rel="noopener noreferrer"` and escaped output
- Add or change services, icons and themes through config alone
- Supports Octane and queue workers
- Tested on PHP 8.4 and 8.5 with Laravel 12 and 13

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [Supported services](#supported-services)
- [Generating links](#generating-links)
- [Share buttons](#share-buttons)
- [Configuration](#configuration)
- [Adding services](#adding-services)
- [API reference](#api-reference)
- [Testing](#testing)
- [Changelog](#changelog)
- [Security](#security)
- [License](#license)

## Requirements

| Dependency | Version        |
|------------|----------------|
| PHP        | 8.4, 8.5       |
| Laravel    | 12.x, 13.x     |

## Installation

```bash
composer require siberfx/share
```

The service provider and the `Share` facade register themselves through
Laravel package discovery. You don't need to configure anything else.

To customise the configuration or views, publish them:

```bash
php artisan vendor:publish --tag=social-share-config
php artisan vendor:publish --tag=social-share-views
```

## Quick start

Add the icon stylesheet to your layout's `<head>` and render the buttons
wherever you need them:

```blade
<head>
    <x-social-share::styles icons="fontawesome" />
</head>

<x-social-share::buttons
    :title="$post->title"
    :services="['x', 'facebook', 'linkedin', 'whatsapp', 'email']"
/>
```

If you only need a URL, use the facade:

```php
use Siberfx\Share\Facade\Share;

Share::load('https://example.com/post', 'My post')->x();
// https://x.com/intent/post?url=https%3A%2F%2Fexample.com%2Fpost&text=My%20post
```

## Supported services

| Service   | Key          | Font Awesome | Line Awesome | Bootstrap Icons |
|-----------|--------------|:------------:|:------------:|:---------------:|
| Bluesky   | `bluesky`    | ✓            | –            | ✓               |
| Delicious | `delicious`  | ✓            | ✓            | –               |
| Digg      | `digg`       | ✓            | ✓            | –               |
| Email     | `email`      | ✓            | ✓            | ✓               |
| Evernote  | `evernote`   | ✓            | ✓            | –               |
| Facebook  | `facebook`   | ✓            | ✓            | ✓               |
| Gmail     | `gmail`      | ✓            | ✓            | ✓               |
| LinkedIn  | `linkedin`   | ✓            | ✓            | ✓               |
| Pinterest | `pinterest`  | ✓            | ✓            | ✓               |
| Reddit    | `reddit`     | ✓            | ✓            | ✓               |
| Telegram  | `telegramMe` | ✓            | ✓            | ✓               |
| Threads   | `threads`    | ✓            | –            | ✓               |
| Tumblr    | `tumblr`     | ✓            | ✓            | –               |
| Twitter   | `twitter`    | ✓            | ✓            | ✓               |
| VK        | `vk`         | ✓            | ✓            | –               |
| WhatsApp  | `whatsapp`   | ✓            | ✓            | ✓               |
| X         | `x`          | ✓            | –            | ✓               |

✓ brand icon · – the icon set has no brand icon, so a generic share icon is used instead.

## Generating links

### A single link

Every configured service can be called as a method. `load()` takes the URL to
share, an optional title, and an optional media URL (used by Pinterest and VK):

```php
Share::load('https://example.com/post', 'My post')->facebook();

Share::load('https://example.com/post', 'My post', 'https://example.com/cover.jpg')->pinterest();
```

Calling a service that isn't configured throws a `BadMethodCallException`.
To check first, use `has()`:

```php
if (Share::has('mastodon')) {
    // ...
}
```

### Several links at once

Pass service keys as arguments or as an array. Call `services()` with no
arguments to get every configured service:

```php
Share::load($url, $title)->services('facebook', 'linkedin', 'x');
Share::load($url, $title)->services(['facebook', 'linkedin', 'x']);
Share::load($url, $title)->services();
```

```php
[
    'facebook' => 'https://facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.com%2Fpost&title=My%20post',
    'linkedin' => 'https://linkedin.com/shareArticle?url=https%3A%2F%2Fexample.com%2Fpost&title=My%20post&mini=true',
    'x'        => 'https://x.com/intent/post?url=https%3A%2F%2Fexample.com%2Fpost&text=My%20post',
]
```

Pass `true` as the last argument to get an object instead of an array:

```php
$links = Share::load($url, $title)->services('facebook', 'x', true);

$links->facebook;
```

### In Blade

```blade
<a href="{{ Share::load(url()->current(), $post->title)->x() }}" target="_blank" rel="noopener noreferrer">
    Share on X
</a>
```

## Share buttons

### Icon sets and themes

| Icon set          | Key               | Version |
|-------------------|-------------------|---------|
| Font Awesome Free | `fontawesome`     | 7.3.1   |
| Line Awesome      | `lineawesome`     | 1.3.0   |
| Bootstrap Icons   | `bootstrap-icons` | 1.13.1  |

| Theme        | Key         |
|--------------|-------------|
| Bootstrap 5  | `bootstrap` |
| Tailwind CSS | `tailwind`  |
| Plain HTML   | `plain`     |

### Loading the icon stylesheet

The styles component prints a `<link>` tag pinned to the version above, with a
Subresource Integrity hash:

```blade
<x-social-share::styles icons="bootstrap-icons" />

{{-- equivalent --}}
{{ Share::styles('bootstrap-icons') }}
```

If your application already bundles the icon set (for example through npm),
skip this step.

### Rendering buttons

```blade
<x-social-share::buttons
    url="https://example.com/post"
    :title="$post->title"
    :services="['x', 'facebook', 'linkedin', 'whatsapp', 'email']"
    icons="fontawesome"
    theme="tailwind"
    labels
/>
```

| Attribute  | Default                      | Description                          |
|------------|------------------------------|--------------------------------------|
| `url`      | `url()->current()`           | URL to share                         |
| `title`    | `''`                         | Title or text to share               |
| `media`    | `''`                         | Image URL (Pinterest, VK)            |
| `services` | all configured services      | Services to display, in order        |
| `icons`    | `social-share.icons.default` | Icon set key                         |
| `theme`    | `social-share.theme`         | Theme key or a custom view name      |
| `labels`   | `false`                      | Show service names next to the icons |

The same output is available from PHP:

```php
Share::load($url, $title)->render(
    services: ['x', 'facebook'],
    iconSet: 'lineawesome',
    theme: 'bootstrap',
    labels: true,
);
```

How buttons behave:

- Buttons without visible labels get an `aria-label`.
- `mailto:` and `whatsapp:` links open in the same tab. All other links open in a new tab with `rel="noopener noreferrer"`.
- Each button has a `social-share-{service}` class for custom styling.

### Using the Tailwind theme

Tailwind only generates the classes it finds in your source files, so add the
package views to its sources.

**Tailwind CSS v4** (`resources/css/app.css`):

```css
@source "../../vendor/siberfx/share/src/views";
```

**Tailwind CSS v3** (`tailwind.config.js`):

```js
content: [
    // ...
    './vendor/siberfx/share/src/views/**/*.blade.php',
],
```

### Custom markup

`buttons()` returns the button data without any markup, and `icon()` returns
the icon classes for one service:

```php
Share::load($url, $title)->buttons(['x', 'email'], 'bootstrap-icons');
// [
//     'x' => [
//         'service'  => 'x',
//         'label'    => 'X',
//         'url'      => 'https://x.com/intent/post?url=...',
//         'icon'     => 'bi bi-twitter-x',
//         'external' => true,
//     ],
//     'email' => [ ... 'icon' => 'bi bi-envelope', 'external' => false ],
// ]

Share::icon('whatsapp', 'lineawesome'); // "lab la-whatsapp"
```

Any Blade view can serve as a theme. It receives `$buttons` (as above) and
`$labels` (bool):

```php
Share::load($url)->render(theme: 'partials.share-buttons');
```

## Configuration

Published to `config/social-share.php`:

| Key             | Default         | Description                                              |
|-----------------|-----------------|----------------------------------------------------------|
| `separator`     | `&`             | Query string separator used by generated links           |
| `services`      | 17 services     | Service definitions (see [Adding services](#adding-services)) |
| `theme`         | `bootstrap`     | Default button theme                                     |
| `icons.default` | `fontawesome`   | Default icon set                                         |
| `icons.sets`    | three icon sets | Stylesheet, fallback and per-service icon classes        |

Set `separator` to `&amp;` only if you print links unescaped (`{!! !!}`) into
HTML attributes. Rendered buttons handle escaping themselves either way.

### Custom icon sets

Override individual icons or register your own icon set:

```php
'theme' => 'tailwind',

'icons' => [
    'default' => 'my-icons',

    'sets' => [
        // ...built-in sets...

        'my-icons' => [
            'stylesheet' => 'https://cdn.example.com/my-icons.css', // optional
            'integrity'  => 'sha384-...',                            // optional
            'fallback'   => 'icon icon-share',
            'icons'      => [
                'x'        => 'icon icon-x',
                'facebook' => 'icon icon-facebook',
            ],
        ],
    ],
],
```

If you host the icon set yourself, leave out `stylesheet`; `styles()` then
returns an empty string.

## Adding services

### Query-string services

Most platforms only need a configuration entry:

```php
'services' => [
    'acme' => [
        'label'     => 'Acme',                             // button label
        'uri'       => 'https://share.acme.example/post',  // may already contain a query string
        'urlName'   => 'url',                              // URL parameter    (default: url)
        'titleName' => 'text',                             // title parameter  (default: title)
        'mediaName' => 'image',                            // media parameter  (omitted when unset)
        'extra'     => ['via' => 'acme'],                  // fixed extra parameters
        'only'      => ['url', 'title'],                   // values to send   (default: url, title, media)
    ],
],
```

The new service is then available as `Share::load($url)->acme()`, in
`services()` and in the rendered buttons.

### View-based services

For URLs that need a custom format, point the service at a Blade view:

```php
'mynewservice' => ['label' => 'My Service', 'view' => 'share.mynewservice'],
```

The view receives:

| Variable   | Description                                        |
|------------|----------------------------------------------------|
| `$service` | The service definition                             |
| `$sep`     | The query string separator                         |
| `$url`     | The URL being shared                               |
| `$title`   | The title being shared                             |
| `$media`   | The media URL being shared                         |

```blade
mailto:?subject={{ rawurlencode("Have a look: $title") }}<?php echo $sep; ?>body={{ rawurlencode("$title — $url") }}
```

Use `<?php echo $sep; ?>` rather than `{{ $sep }}` so the separator is not
HTML-escaped. The view output is trimmed, so trailing newlines are ignored.

To localise the text, use Laravel's translator inside the view:

```blade
mailto:?subject={{ rawurlencode(__('share.email-subject', compact('title'))) }}<?php echo $sep; ?>body={{ rawurlencode(__('share.email-body', compact('title', 'url'))) }}
```

Keep generated URLs under roughly 2,000 characters for broad client
compatibility.

## API reference

| Method                                                        | Returns                  | Description                                  |
|---------------------------------------------------------------|--------------------------|----------------------------------------------|
| `load(?string $url, ?string $title = '', ?string $media = '')` | `Share`                  | Set the content to share                     |
| `{service}()`                                                 | `string`                 | Share URL for a configured service           |
| `services(...$services)`                                      | `array\|object`          | Share URLs for several (or all) services     |
| `has(string $service)`                                        | `bool`                   | Whether a service is configured              |
| `generateUrl(string $service, ?string $separator = null)`     | `string`                 | Share URL for a service by name              |
| `render($services = null, $iconSet = null, $theme = null, $labels = false)` | `HtmlString` | Rendered share buttons                  |
| `buttons($services = null, ?string $iconSet = null)`          | `array`                  | Button data without markup                   |
| `icon(string $service, ?string $iconSet = null)`              | `string`                 | Icon CSS classes for a service               |
| `styles(?string $iconSet = null)`                             | `HtmlString`             | Stylesheet `<link>` tag for an icon set      |

Unknown services throw `BadMethodCallException`; unknown icon sets throw
`InvalidArgumentException`.

## Testing

```bash
composer test
```

The test suite runs in CI against PHP 8.4 and 8.5 with Laravel 12 and 13, using
both the oldest and the newest allowed dependency versions.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release notes. This package follows
[Semantic Versioning](https://semver.org).

## Security

If you discover a security vulnerability, please email
[info@siberfx.com](mailto:info@siberfx.com) instead of opening a public issue.

## License

Released under the [MIT License](https://opensource.org/licenses/MIT).
