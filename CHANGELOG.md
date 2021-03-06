# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased] -- 2018-MM-DD

### Added
  * `composer format` command for standardized formatting (uses `php-cs-fixer`)
### Changed
### Deprecated
### Removed
### Fixed
### Security

## [1.4.0]

### Added
  * login() defined on Gateway to allow retrieval of API-defined copy
  * all request methods now update the jwt with response
  * Gateway getBuyerProfile()
  * Gateway getSupplierProfile()

### Changed
  * login() on Gateway now uses v1/gateway/login, returns api defined copy

## [1.3.0] -- 2018-01-31

### Fixed
  * Correctly decode JWT claims
  * Use correct endpoint for `login()`

## [1.2.0] -- 2018-01-22

### Added
  * confirmForShipment()
  * getConfig()
### Fixed
  * Appropriately test change introduced in 1.1.1

## [1.1.1] -- 2017-12-06

### Fixed
  * Encode nested array-like payload values as JSON

## [1.1.0] -- 2017-12-05

### Added
  * createShipment()
### Changed
  * Allow protocol to be set
### Fixed
  * Handle PHP errors appropriately
  * Updated assertion in testCreatePurchaseOrder()

## [1.0.0] -- 2017-11-06

### Added
  * Initial release
