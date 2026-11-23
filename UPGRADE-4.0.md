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

### Mapping built on first access, `EntityMappingListener` removed

In 3.x the mapping (`austral.entity.mapping`) was built by `Austral\EntityBundle\Listener\EntityMappingListener` on `kernel.request`. It was therefore empty in console commands, Messenger workers and Doctrine listeners run outside an HTTP request, and it was built again on every sub-request.

The mapping is now built by `Austral\EntityBundle\Mapping\Mapping` itself, the first time it is read, once per service instance. The `austral.event.entity.initialise.mapping` event and `EntityMappingEvent` do not change: listeners registered on this event keep working.

Removed:

- the `Austral\EntityBundle\Listener\EntityMappingListener` class and its `initEntityAnnotations()` method;
- the `austral.entity.mapping.listener` service.

If your code called `initEntityAnnotations()` to make sure the mapping was ready, remove the call:

```diff
- $container->get('austral.entity.mapping.listener')->initEntityAnnotations();
  $entityMapping = $container->get('austral.entity.mapping')->getEntityMapping(Page::class);
```

The constructor of `Mapping` has two new arguments (the event dispatcher and a closure returning `EntitiesAttributes`). This only matters if you create the service yourself instead of using `austral.entity.mapping`.

### Mapping: value accessors instead of closures

`FieldMapping` and `EntityClassMapping` no longer accept getter and setter closures. The methods `addGetterObjectValueFonction()` and `addSetterObjectValueFonction()` are removed, without alias. A field or a class now reads and writes its values through an object that implements `Austral\EntityBundle\Mapping\ValueAccessor\ValueAccessorInterface`. Without one, `EntityValueAccessor` is used: it calls `getValueByFieldname()` / `setValueByFieldname()` on the entity, as 3.x did without closures.

Before:

```php
$fieldMapping->addGetterObjectValueFonction(function (EntityInterface $object, string $fieldname): mixed {
  return $object->getTranslateCurrent()?->getValueByFieldname($fieldname);
});
$fieldMapping->addSetterObjectValueFonction(function (EntityInterface $object, string $fieldname, mixed $value): void {
  $object->getTranslateCurrent()?->setValueByFieldname($fieldname, $value);
});
```

After (entity-translate-bundle ships such an accessor, `TranslatableValueAccessor`):

```php
final class TranslateCurrentValueAccessor implements ValueAccessorInterface
{
  public function getValue(EntityInterface $object, string $fieldname): mixed
  {
    return $object instanceof TranslateMasterInterface ? $object->getTranslateCurrent()?->getValueByFieldname($fieldname) : null;
  }

  public function setValue(EntityInterface $object, string $fieldname, mixed $value = null): void
  {
    if ($object instanceof TranslateMasterInterface)
    {
      $object->getTranslateCurrent()?->setValueByFieldname($fieldname, $value);
    }
  }
}

$fieldMapping->setValueAccessor(new TranslateCurrentValueAccessor());
```

An accessor must hold no closure and no service: it is kept in the mapping, which will be cached.

`FieldMappingInterface` and `EntityClassMappingInterface` declare `setValueAccessor()` and `getValueAccessor()`. A class of yours that implements one of these interfaces without extending `FieldMapping` or `EntityClassMapping` must add them.

> **Warning:** in 3.x, `setObjectValue()` called the setter closure **and then** `setValueByFieldname()` on the entity, so the value was written twice. It is now written once, by the accessor. If your setter closure relied on the second write, move that write into your accessor.

### Services: explicit definitions, private class ids

The services of the bundle are no longer autowired nor autoconfigured: each one is defined with its arguments and tags. As a result, the service ids named after a class are **private**. Only the `austral.entity.*` aliases are public, and they are the supported way to get a service from the container.

Before:

```php
$mapping = $container->get(\Austral\EntityBundle\Mapping\Mapping::class);
```

After:

```php
$mapping = $container->get('austral.entity.mapping');
```

Injecting a service is not affected: autowiring `Austral\EntityBundle\Mapping\Mapping` (or any other class of the bundle) in a constructor of your application keeps working, and so does a reference to an alias in YAML (`'@austral.entity.mapping'`).

| Class | Public alias |
|-------|--------------|
| `Services\Sanitizer` | `austral.entity.sanitizer` |
| `Services\InitialiseBundleEntities` | `austral.entity.initialise-bundle` |
| `EntityAttributes\EntitiesAttributes` | `austral.entity.entities-attributes` |
| `Mapping\Mapping` | `austral.entity.mapping` |
| `EntityManager\EntityManager` | `austral.entity_manager` |
| `Listener\DoctrineListener` | `austral.entity.doctrine.listener` |
| `EventSubscriber\EntityManagerEventSubscriber` | `austral.entity.manager.event_subscriber` |
| `EventSubscriber\EntityMappingEventSubscriber` | `austral.entity.mapping.event_subscriber` |

### Custom sanitizers are registered automatically

A service of your application that implements `Austral\EntityBundle\Sanitizer\AustralSanitizerInterface` is now tagged `austral_entity.sanitizer_rule` automatically (with the default `autoconfigure: true` of an application). In 3.x the tag was only added to the sanitizers of the bundle, so yours were ignored unless you tagged them yourself. If you tagged them by hand, you can remove the tag.

### `EntityManager`: protected properties renamed

| 3.1 | 4.0 |
|-----|-----|
| `$this->em` | `$this->doctrineEntityManager` |
| `$this->dispatcher` | `$this->eventDispatcher` |

The constructor arguments are renamed the same way (`$doctrineEntityManager`, `$eventDispatcher`). If one of your entity managers extends `Austral\EntityBundle\EntityManager\EntityManager`, update it:

```bash
grep -rlE '\$this->(em|dispatcher)\b' src/ | xargs sed -i -E 's/\$this->em\b/$this->doctrineEntityManager/g; s/\$this->dispatcher\b/$this->eventDispatcher/g'
```

> **Warning:** check the result: this command also renames `$this->em` and `$this->dispatcher` in classes that do not extend the Austral entity manager. Run it only on your entity managers.

### `QueryBuilderEventSubscriber` removed

`Austral\EntityBundle\EventSubscriber\QueryBuilderEventSubscriber` and the `austral.entity.query_builder.event_subscriber` service are removed: the listener did nothing. `AustralQueryBuilder::queryCondition()` still dispatches `QueryBuilderEvent::EVENT_CONDITION`, so your own listeners on this event keep working.

<!--
For each break: what changes, why, and a before / after example.

### Annotations replaced by attributes

Before:
    /** @Austral\Field(...) */
After:
    #[Austral\Field(...)]
-->
