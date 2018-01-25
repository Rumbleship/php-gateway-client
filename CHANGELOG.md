# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
### Changed
### Deprecated
### Removed
### Fixed
  * Correctly decode JWT claims
### Security

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
