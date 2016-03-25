# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased][unreleased]
- All content has been released to date.

## [1.0.3] - 2016-03-25
### Added
- Support for attachments, inline attachments, inline css, sandbox, start time, and transactional options in `Transmission` class
- API response exceptions now include message, code, and description from API

## [1.0.2] - 2016-02-28
### Fixed
- Miscellaneous code cleanups related to docs and namespacing

## [1.0.1] - 2016-02-24
### Added
- Example for using `setupUnwrapped()` to get a list of webhooks.
- CHANGELOG.md for logging release updates and backfilled it with previous release.

### Fixed
- Library will now throw a `SparkPost\APIReponseException` properly when a 4XX http status is encountered.

## 1.0.0 - 2015-10-15
### Added
- Request adapter interface for passing in request adapters via `Ivory\HttpAdapter`
- Ability to create 'unwrapped' modules for API endpoints that haven't had functionality included yet.
- Instructions for setting up request adapters in README 

### Changed
- Library now requires PHP 5.5 or greater
- Updated interface to be instance based with referenceable objects rather than static functions.

### Fixed
- README now has proper code blocks denoting PHP language

[unreleased]: https://github.com/sparkpost/php-sparkpost/compare/v1.0.3...HEAD
[1.0.3]: https://github.com/sparkpost/php-sparkpost/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/sparkpost/php-sparkpost/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/sparkpost/php-sparkpost/compare/v1.0.0...v1.0.1

