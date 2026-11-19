# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

### Changed

- Sources moved to `src/`, tests to `tests/` (PSR-4 `Austral\EntityBundle\` → `src/`).
- Dependencies used by the bundle are now declared in `composer.json` (tools-bundle, Doctrine collections / DBAL, Symfony components).
- **BREAKING** `paginatorByQueryBuilder()` / `paginatorByClosure()` return a `Austral\EntityBundle\Paginator\PaginatorResult` instead of an `array{count, objects}`; pagination uses Doctrine ORM's `OffsetPaginator` (the ORM `Paginator` is deprecated since ORM 3.7).
- **BREAKING** `Entity::serialize()` returns the JSON representation (same as `json()`) instead of a PHP `serialize()` string.
- `countByQueryBuilder()` counts distinct root entities (`COUNT(DISTINCT <root alias>)`), ignoring the `ORDER BY` and the pagination of the given QueryBuilder.
- `InitialiseBundleEntities` and `InitialiseBundleEntitiesCommand` receive their dependencies through the constructor instead of the service container.
- `EntityManagerEventSubscriber` uses the PropertyAccess component to read and write entity fields; a `PropertyAccessorInterface` can be injected.

### Deprecated

- `Entity::serialize()` has been removed from `Austral\EntityBundle\Entity\Entity` and from `Austral\Contracts\Entity\EntityInterface`.
- `Austral\EntityBundle\Entity\EntityInterface`, use `Austral\Contracts\Entity\EntityInterface` (austral/contracts).

### Fixed

- `retrieveByKey()` throws an `\InvalidArgumentException` when the key is not a mapped field of the entity, instead of building DQL from an arbitrary string.
- `truncate()` works on MySQL / MariaDB with any driver (platform check instead of the `pdo_mysql` driver class), no longer fails on the implicit commit of `TRUNCATE`, and always restores the foreign key checks.
- `selectAll()`, `selectByQueryBuilder()` and `selectByClosure()` return an `array` again, as in 3.x (the `?Collection` return type did not match the returned value); the `EntityManager` versions return `[]` when no repository is set.
- `countAll()` and `countByQueryBuilder()` no longer return `0` when the database driver returns the count as a numeric string.
- `countByQueryBuilder()` no longer modifies the given QueryBuilder and supports any root alias.
- `EntityManager::flush()` dispatches the pending `push.before` / `push.after` events only once; `deletes()` now dispatches them.
- `EntityManager::getRepository($class)` no longer replaces the repository of the entity manager.
- `duplicate()` works with the Doctrine ORM 3 `FieldMapping` objects, no longer calls `setId(null)` on entities with a non-string identifier, and supports embeddable fields.

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
