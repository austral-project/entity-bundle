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

<!--
For each break: what changes, why, and a before / after example.

### Annotations replaced by attributes

Before:
    /** @Austral\Field(...) */
After:
    #[Austral\Field(...)]
-->
