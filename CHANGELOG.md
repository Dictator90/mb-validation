# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Strict handling for unknown string rules with optional backward-compatible mode.
- Class-based `email`, `exists`, and `unique` rules in the default registry.
- CI workflow for PHP 8.2/8.3/8.4 with tests and dependency audit.
- MIT `LICENSE`, `phpunit.xml.dist`, and package metadata (`homepage`, `support`, `repository`).

### Changed
- `ValidationException::withMessages()` now uses package-native factory/validator instead of framework facades.
- Unified default locale to Russian (`ru`) for `Factory::create()` and `new Factory()`.
- Declared minimum supported PHP version raised to `>=8.2`.
- `ValidatorInterface` now includes `validate(): array`.
