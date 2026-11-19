# Upgrade from 3.1 to 4.0

## Requirements

- PHP 8.4 minimum
- Symfony 7.4
- Doctrine ORM 3

## Update the dependencies

```diff
- "austral/entity-bundle": "^3.1"
+ "austral/entity-bundle": "^4.0"
```

All `austral/*` packages must be upgraded to `^4.0` at the same time.

## Breaking changes

### `EntityManager::$user` removed

This protected property was never read nor written by Austral. If your own entity manager extends `Austral\EntityBundle\EntityManager\EntityManager` and uses `$this->user`, declare the property in your class.

### `paginatorByQueryBuilder()` and `paginatorByClosure()` return a `PaginatorResult`

In 3.x these methods (on the repository and on the entity manager) returned an array with the `count` and `objects` keys. They now return an `Austral\EntityBundle\Paginator\PaginatorResult`.

Before:

```php
$result = $entityManager->paginatorByQueryBuilder($queryBuilder);
$count = $result['count'];
$objects = $result['objects'];
```

After:

```php
$result = $entityManager->paginatorByQueryBuilder($queryBuilder);
$count = $result->getCount();
$objects = $result->getObjects();
```

When the query fails, `getCount()` returns `0`, `getObjects()` returns `[]` and `getException()` returns the exception that was raised.

### `Entity::serialize()` returns JSON

`Entity::serialize()` returned `serialize($this->arrayObject())`. It now returns the same JSON string as `Entity::json()` and is deprecated. If you need the PHP serialized form, call `serialize($entity->arrayObject())` in your code.

<!--
For each break: what changes, why, and a before / after example.

### Annotations replaced by attributes

Before:
    /** @Austral\Field(...) */
After:
    #[Austral\Field(...)]
-->
