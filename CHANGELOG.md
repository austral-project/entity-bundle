# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

### Changed

- Sources moved to `src/`, tests to `tests/` (PSR-4 `Austral\EntityBundle\` → `src/`).
- Dependencies used by the bundle are now declared in `composer.json` (tools-bundle, Doctrine collections / DBAL, Symfony components).

### Fixed

### Removed

- `EntityManager::$user`: unused property, removed (entity-bundle no longer depends on symfony/security-core).

## [3.1.0] - 2022-09-17

- Added the new Entities Interfaces
- Changed the check if object is a TranslateMasterInterface

## [3.0.0] - 2021-02-10

- Created new repository and new branch in to Github
- Changed the project License to the MIT
- Writing and publish documentation
- Add to Packagist Service for installation with composer

## 2.x and 1.x

These versions were not open source and have no public documentation.

[Unreleased]: https://github.com/austral-project/entity-bundle/compare/v3.1.2...4.x
[3.1.0]: https://github.com/austral-project/entity-bundle/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/austral-project/entity-bundle/releases/tag/v3.0.0
