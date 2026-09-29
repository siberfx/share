# Changelog

All notable changes to `siberfx/share` are documented here. This project adheres
to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- PHP 8.5 support (`"php": "^8.4|^8.5"`), verified against Laravel 12 and 13.
- New services: `x` (x.com), `bluesky` and `threads`.
- `Share::has($service)` to check whether a service is configured.
- `Siberfx\Share\Share` is resolvable from the container by class name.
- Separate publish tags: `social-share-config` and `social-share-views`.
- GitHub Actions matrix (PHP 8.4/8.5 × Laravel 12/13 × lowest/stable) and README badges.
- Facade `@method` annotations for IDE autocompletion.

### Changed
- Default (non-view) links are built in PHP instead of the `social-share::default` Blade view,
  avoiding HTML escaping and whitespace issues. The `default`, `mock` and `test` views were removed.
- The `share` binding is now `scoped`, so Octane/queue workers get a fresh instance per request.
- Removed the unused `$defer`/`provides()` from the service provider; publishing only happens in console.
- Removed the unused `guzzlehttp/guzzle` dev dependency.
- **BREAKING:** Require PHP `^8.4` and Laravel `^12.0|^13.0` only (dropped Laravel 10/11).
- Migrated autoloading from deprecated **PSR-0** to **PSR-4** (`Siberfx\Share\` → `src/Siberfx/Share/`).
- Updated dev dependencies for the modern toolchain: `orchestra/testbench ^10.0|^11.0`,
  `mockery/mockery ^1.6`, `guzzlehttp/guzzle ^7.8`, and added `phpunit/phpunit ^11.5|^12.0`.
- Migrated `phpunit.xml` to the PHPUnit 11/12 schema (removed attributes that no longer
  exist; `@group live` annotations converted to `#[Group('live')]` attributes).
- Refreshed test fixtures to the current `social-share` config (https URLs) and removed
  defunct services (`gplus`, `scoopit`, `viadeo`).

### Fixed
- Calling an undefined service (e.g. a typo like `->twiter()`) silently returned a broken
  `?url=...` link; it now throws a `BadMethodCallException`.
- `services([...], true)` ignored the `true` flag when services were passed as an array.
- A service `uri` that already contains a query string produced `?a=1&amp;b=2?url=...`
  (HTML-escaped and a second `?`); parameters are now appended correctly.
- The `email` view ignored the configured `separator`.
- `whatsapp` produced a leading `%20` when no title was given.
- The `only` option crashed with `Undefined variable $url` when `url` was not listed, and
  could expose arbitrary object properties; it is now restricted to `url`, `title`, `media`.
- A title of `"0"` was dropped; `null` title/media are now accepted.
- `Share::generateUrl()` now imports the proper facades (`Illuminate\Support\Facades\View`,
  `Illuminate\Support\Arr`) instead of relying on global class aliases.
- `default.blade.php`: replaced `http_build_query($extra, null, ...)` with `''` — passing
  `null` to the string `$numeric_prefix` parameter is deprecated as of PHP 8.1.
- `default.blade.php`: guard against an undefined `uri` key (PHP 8.x escalates this to a
  warning/exception) for misconfigured services.
- Corrected the package facade alias to `Siberfx\Share\Facade\Share` (was the non-existent
  `Siberfx\Share\ShareFacade`) in tests and README.
