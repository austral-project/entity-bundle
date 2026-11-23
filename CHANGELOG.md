# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

- `Austral\EntityBundle\Mapping\ValueAccessor\ValueAccessorInterface` and its default implementation `EntityValueAccessor`: how the mapping reads and writes a field value. They hold no closure, so the mapping can be cached.
- `Austral\EntityBundle\PropertyAccess\FieldAccessor`: checks whether a field can be read or written with the PropertyAccess rules (getters, `is`/`has`/`can`, setters, adders, public properties), without magic methods and without an instance (`symfony/property-info`).
### Changed

- Sources moved to `src/`, tests to `tests/` (PSR-4 `Austral\EntityBundle\` → `src/`).
- Dependencies used by the bundle are now declared in `composer.json` (tools-bundle, Doctrine collections / DBAL, Symfony components).
- **BREAKING** `retreiveById()`, `retreiveByKey()` and `retreiveByClosure()` are renamed `retrieveById()`, `retrieveByKey()` and `retrieveByClosure()`, on the repository and on the entity manager. Their `$closure` argument is typed `?callable`.
- **BREAKING** `paginatorByQueryBuilder()` / `paginatorByClosure()` return a `Austral\EntityBundle\Paginator\PaginatorResult` instead of an `array{count, objects}`; pagination uses Doctrine ORM's `OffsetPaginator` (the ORM `Paginator` is deprecated since ORM 3.7).
- `countByQueryBuilder()` counts distinct root entities (`COUNT(DISTINCT <root alias>)`), ignoring the `ORDER BY` and the pagination of the given QueryBuilder.
- `InitialiseBundleEntities` and `InitialiseBundleEntitiesCommand` receive their dependencies through the constructor instead of the service container.
- `EntityManagerEventSubscriber` uses the PropertyAccess component to read and write entity fields; a `PropertyAccessorInterface` can be injected.
- **BREAKING** `Mapping` builds itself on first access (it dispatches `austral.event.entity.initialise.mapping` once per service instance) instead of being built on `kernel.request`. Its constructor receives the event dispatcher and a closure returning `EntitiesAttributes`.
- **BREAKING** The getter and setter closures of the mapping are replaced by value accessor objects: `FieldMapping` and `EntityClassMapping` (and their interfaces) get `setValueAccessor()` / `getValueAccessor()`, and use `EntityValueAccessor` by default.
- **BREAKING** Services are defined explicitly (no autowiring, no autoconfiguration). Service ids named after a class (`Austral\EntityBundle\…`) are private: use the public `austral.entity.*` aliases to get a service from the container. The aliases of the listeners and subscribers are private. Autowiring a class of the bundle in an application still works.
- **BREAKING** The protected properties of `EntityManager` are renamed: `$em` → `$doctrineEntityManager`, `$dispatcher` → `$eventDispatcher` (and the constructor arguments accordingly).

### Deprecated

### Fixed

- `FieldMapping::setObjectValue()` no longer writes the value twice when a custom setter is set (the closure was called, then `setValueByFieldname()` as well).
- Sanitizers of the application that implement `AustralSanitizerInterface` are tagged `austral_entity.sanitizer_rule` automatically (`registerForAutoconfiguration()`). The `_instanceof` rule of 3.x only applied to the services of the bundle, so they were ignored.
- `retrieveByKey()` throws an `\InvalidArgumentException` when the key is not a mapped field of the entity, instead of building DQL from an arbitrary string.
- `truncate()` works on MySQL / MariaDB with any driver (platform check instead of the `pdo_mysql` driver class), no longer fails on the implicit commit of `TRUNCATE`, and always restores the foreign key checks.
- `selectAll()`, `selectByQueryBuilder()` and `selectByClosure()` return an `array` again, as in 3.x (the `?Collection` return type did not match the returned value); the `EntityManager` versions return `[]` when no repository is set.
- `countAll()` and `countByQueryBuilder()` no longer return `0` when the database driver returns the count as a numeric string.
- `countByQueryBuilder()` no longer modifies the given QueryBuilder and supports any root alias.
- `EntityManager::flush()` dispatches the pending `push.before` / `push.after` events only once; `deletes()` now dispatches them.
- `EntityManager::getRepository($class)` no longer replaces the repository of the entity manager.
- `duplicate()` works with the Doctrine ORM 3 `FieldMapping` objects, no longer calls `setId(null)` on entities with a non-string identifier, and supports embeddable fields.
- The mapping is available in console commands, Messenger workers and Doctrine listeners run outside an HTTP request (it was empty there, so commands such as `austral:file:compress` did nothing).
- The mapping is no longer built again on each sub-request, which registered every field mapping several times (`getAllFieldsMappingClassByFieldname()` returned duplicates).

### Removed

- **BREAKING** `Austral\EntityBundle\Entity\EntityInterface`: use `Austral\Contracts\Entity\EntityInterface` (austral/contracts).
- **BREAKING** `Entity::serialize()`, removed from `Austral\EntityBundle\Entity\Entity` and from `Austral\Contracts\Entity\EntityInterface`: use `json()`.
- `EntityManager::$user`: unused property, removed (entity-bundle no longer depends on symfony/security-core).
- **BREAKING** `Austral\EntityBundle\Listener\EntityMappingListener` and the `austral.entity.mapping.listener` service: the mapping builds itself on first access.
- **BREAKING** `FieldMapping::addGetterObjectValueFonction()`, `addSetterObjectValueFonction()` and the same methods of `EntityClassMapping`: use `setValueAccessor()` with a `ValueAccessorInterface`.
- `Austral\EntityBundle\EventSubscriber\QueryBuilderEventSubscriber` and the `austral.entity.query_builder.event_subscriber` service: its listener was empty. `AustralQueryBuilder::queryCondition()` still dispatches `QueryBuilderEvent::EVENT_CONDITION`.

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
