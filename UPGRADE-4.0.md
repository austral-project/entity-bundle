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

### `EntityInterface` moved to austral/contracts

`Austral\EntityBundle\Entity\EntityInterface` is removed. Use `Austral\Contracts\Entity\EntityInterface`, provided by `austral/contracts` (installed with entity-bundle).

```diff
- use Austral\EntityBundle\Entity\EntityInterface;
+ use Austral\Contracts\Entity\EntityInterface;
```

To update a project:

```bash
grep -rl 'Austral\\EntityBundle\\Entity\\EntityInterface' src/ | xargs sed -i 's/Austral\\EntityBundle\\Entity\\EntityInterface/Austral\\Contracts\\Entity\\EntityInterface/g'
```

### `retreive*()` methods renamed `retrieve*()`

The misspelled methods of the repository and of the entity manager are renamed, without alias:

| 3.1 | 4.0 |
|-----|-----|
| `retreiveById()` | `retrieveById()` |
| `retreiveByKey()` | `retrieveByKey()` |
| `retreiveByClosure()` | `retrieveByClosure()` |

The `$closure` argument is now typed `?callable` (it was `\Closure`).

To update a project:

```bash
grep -rl 'retreiveBy' src/ templates/ | xargs sed -i 's/retreiveBy/retrieveBy/g'
```

> **Warning:** if one of your repositories or entity managers **overrides** a `retreive*()` method, your override is no longer called after the upgrade, and no error is raised. Rename it to `retrieve*()`.

### `Entity::serialize()` removed

`Entity::serialize()` returned `serialize($this->arrayObject())`. It is removed from `Austral\EntityBundle\Entity\Entity` and from `Austral\Contracts\Entity\EntityInterface`.

Before:

```php
$value = $entity->serialize();
```

After:

```php
$value = $entity->json();                       // JSON representation
```

<!--
For each break: what changes, why, and a before / after example.

### Annotations replaced by attributes

Before:
    /** @Austral\Field(...) */
After:
    #[Austral\Field(...)]
-->
