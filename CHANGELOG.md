# Changelog

All notable changes to `siberfx/share` are documented here. This project adheres
to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Changed
- **BREAKING:** Require PHP `^8.4` and Laravel `^12.0|^13.0` only (dropped Laravel 10/11).
- Migrated autoloading from deprecated **PSR-0** to **PSR-4** (`Siberfx\Share\` → `src/Siberfx/Share/`).
- Updated dev dependencies for the modern toolchain: `orchestra/testbench ^10.0|^11.0`,
  `mockery/mockery ^1.6`, `guzzlehttp/guzzle ^7.8`, and added `phpunit/phpunit ^11.5|^12.0`.
- Migrated `phpunit.xml` to the PHPUnit 11/12 schema (removed attributes that no longer
  exist; `@group live` annotations converted to `#[Group('live')]` attributes).
- Refreshed test fixtures to the current `social-share` config (https URLs) and removed
  defunct services (`gplus`, `scoopit`, `viadeo`).

### Fixed
- `Share::generateUrl()` now imports the proper facades (`Illuminate\Support\Facades\View`,
  `Illuminate\Support\Arr`) instead of relying on global class aliases.
- `default.blade.php`: replaced `http_build_query($extra, null, ...)` with `''` — passing
  `null` to the string `$numeric_prefix` parameter is deprecated as of PHP 8.1.
- `default.blade.php`: guard against an undefined `uri` key (PHP 8.x escalates this to a
  warning/exception) for misconfigured services.
- Corrected the package facade alias to `Siberfx\Share\Facade\Share` (was the non-existent
  `Siberfx\Share\ShareFacade`) in tests and README.
