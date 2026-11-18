# Austral Entity

[![Version](https://img.shields.io/packagist/v/austral/entity-bundle?label=version)](https://packagist.org/packages/austral/entity-bundle)
[![CI](https://github.com/austral-project/entity-bundle/actions/workflows/ci.yml/badge.svg?branch=4.x)](https://github.com/austral-project/entity-bundle/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/packagist/dependency-v/austral/entity-bundle/php?version=4.x-dev)](https://packagist.org/packages/austral/entity-bundle)
[![Symfony](https://img.shields.io/badge/symfony-7.4-black)](https://symfony.com/releases/7.4)
[![License](https://img.shields.io/github/license/austral-project/entity-bundle)](LICENSE)

Tools for Symfony Entity

> This bundle is part of [Austral](https://github.com/austral-project), a modular back office and CMS for Symfony.

## Requirements

- PHP 8.2 or higher
- Symfony 7.4

## Installation

```bash
composer require austral/entity-bundle
```

If you do not use Symfony Flex, register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Austral\EntityBundle\AustralEntityBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/austral_entity.yaml
austral_entity:
    # ...
```

## Usage

<!-- Minimal, concrete example. -->

## Documentation

The full documentation is available at https://austral.dev.

## Versions

| Bundle | Symfony | PHP | Branch |
|--------|---------|-----|---------|
| 4.x    | 7.4     | ≥ 8.2 | `4.x` |
| 3.1    | 5.4 / 6.4 | ≥ 8.0 | `3.1` (maintenance) |

See the [CHANGELOG](CHANGELOG.md) and, to upgrade from 3.1, [UPGRADE-4.0](UPGRADE-4.0.md).

## Contributing

Contributions are welcome: see the [contributing guide](https://github.com/austral-project/.github/blob/master/CONTRIBUTING.md). To report a security issue, follow the [security policy](https://github.com/austral-project/.github/blob/master/SECURITY.md).

## License

MIT, see [LICENSE](LICENSE).

## Credits

Created by [Matthieu Beurel](https://www.mbeurel.com). Sponsored by [Yipikai Studio](https://yipikai.studio).
