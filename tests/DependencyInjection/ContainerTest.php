<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\DependencyInjection;

use Austral\EntityBundle\DependencyInjection\AustralEntityExtension;
use Austral\EntityBundle\EntityAttributes\EntitiesAttributes;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\EntityBundle\Sanitizer\AustralSanitizerInterface;
use Austral\EntityBundle\Services\Sanitizer;
use Austral\ToolsBundle\Services\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/**
 * The services are defined explicitly: a missing or misnamed argument only shows up when the container
 * is compiled or the service is created. This test loads the extension, compiles the container and
 * creates every service of the bundle.
 */
final class ContainerTest extends TestCase
{
  /**
   * Public aliases: the supported way to get a service from the container.
   */
  private const array PUBLIC_ALIASES = [
    'austral.entity.sanitizer',
    'austral.entity.initialise-bundle',
    'austral.entity.entities-attributes',
    'austral.entity.mapping',
    'austral.entity.manager.event_subscriber',
    'austral.entity.mapping.event_subscriber',
    'austral.entity_manager',
    'austral.entity.doctrine.listener',
  ];

  /**
   * @return iterable<string, array{string, class-string}>
   */
  public static function serviceIds(): iterable
  {
    foreach (self::serviceClasses() as $id => $class)
    {
      yield $id => [$id, $class];
    }
  }

  /**
   * @param class-string $class
   */
  #[DataProvider('serviceIds')]
  public function testEveryServiceCanBeCreated(string $id, string $class): void
  {
    $container = $this->compiledContainer(publicServices: true);

    self::assertInstanceOf($class, $container->get($id));
  }

  public function testOnlyTheAustralAliasesArePublic(): void
  {
    $container = self::loadExtension();

    foreach ($container->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\EntityBundle\\') || str_starts_with($id, 'austral.entity'))
      {
        self::assertFalse($definition->isPublic(), sprintf('The service "%s" must be private.', $id));
      }
    }
    $publicAliases = [];
    foreach ($container->getAliases() as $id => $alias)
    {
      if ($alias->isPublic())
      {
        $publicAliases[] = $id;
      }
    }
    sort($publicAliases);
    $expected = self::PUBLIC_ALIASES;
    sort($expected);
    self::assertSame($expected, $publicAliases);
  }

  public function testPublicAliasesCanBeFetched(): void
  {
    $classes = self::aliasClasses();
    $container = $this->compiledContainer(publicServices: false);

    foreach (self::PUBLIC_ALIASES as $alias)
    {
      self::assertTrue($container->has($alias), sprintf('The alias "%s" must be public.', $alias));
      self::assertInstanceOf($classes[$alias], $container->get($alias));
    }
  }

  public function testTheSanitizersOfTheBundleAreTagged(): void
  {
    $container = $this->compiledContainer(publicServices: true);

    $sanitizer = $container->get('austral.entity.sanitizer');
    self::assertInstanceOf(Sanitizer::class, $sanitizer);
    $sanitizers = new \ReflectionProperty(Sanitizer::class, 'sanitizers')->getValue($sanitizer);

    self::assertIsArray($sanitizers);
    self::assertSame(['default', 'html'], array_keys($sanitizers));
    self::assertContainsOnlyInstancesOf(HtmlSanitizerInterface::class, $sanitizers);
  }

  public function testSanitizersOfTheApplicationAreTaggedAutomatically(): void
  {
    $container = self::loadExtension();
    $container->setDefinition('app.sanitizer', new Definition(AppSanitizer::class)->setAutoconfigured(true));

    $container->compile();

    self::assertArrayHasKey('app.sanitizer', $container->findTaggedServiceIds('austral_entity.sanitizer_rule'));
  }

  public function testTheMappingReceivesAClosureThatReturnsTheEntitiesAttributes(): void
  {
    $container = $this->compiledContainer(publicServices: true);
    $mapping = $container->get('austral.entity.mapping');
    self::assertInstanceOf(Mapping::class, $mapping);

    $closure = new \ReflectionProperty(Mapping::class, 'entitiesAttributes')->getValue($mapping);

    self::assertInstanceOf(\Closure::class, $closure);
    self::assertInstanceOf(EntitiesAttributes::class, $closure());
  }

  /**
   * @return array<string, class-string> the class of each service of the bundle, by service id
   */
  private static function serviceClasses(): array
  {
    $classes = [];
    foreach (self::loadExtension()->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\EntityBundle\\') || str_starts_with($id, 'austral.entity'))
      {
        $class = $definition->getClass() ?? $id;
        if (!class_exists($class))
        {
          self::fail(sprintf('The class "%s" of the service "%s" does not exist.', $class, $id));
        }
        $classes[$id] = $class;
      }
    }

    return $classes;
  }

  /**
   * @return array<string, class-string> the class of the service behind each public alias
   */
  private static function aliasClasses(): array
  {
    $container = self::loadExtension();
    $services = self::serviceClasses();
    $classes = [];
    foreach (self::PUBLIC_ALIASES as $alias)
    {
      self::assertTrue($container->hasAlias($alias), sprintf('The alias "%s" is not defined.', $alias));
      $target = (string) $container->getAlias($alias);
      self::assertArrayHasKey($target, $services, sprintf('The alias "%s" points to "%s", which is not a service of the bundle.', $alias, $target));
      $classes[$alias] = $services[$target];
    }

    return $classes;
  }

  /**
   * The services that the bundle needs from the application (Doctrine, event dispatcher, tools-bundle)
   * are added as minimal definitions.
   */
  private static function loadExtension(): ContainerBuilder
  {
    $container = new ContainerBuilder();
    $container->setParameter('kernel.project_dir', sys_get_temp_dir());
    $container->setParameter('kernel.bundles', []);
    $container->setDefinition('doctrine.orm.entity_manager', new Definition(EntityManagerInterface::class)->setSynthetic(true)->setPublic(true));
    $container->setDefinition('event_dispatcher', new Definition(EventDispatcher::class));
    $container->setDefinition('austral.tools.debug', new Definition(Debug::class));
    new AustralEntityExtension()->load([], $container);

    return $container;
  }

  private function compiledContainer(bool $publicServices): ContainerBuilder
  {
    $container = self::loadExtension();
    if ($publicServices)
    {
      foreach ($container->getDefinitions() as $id => $definition)
      {
        if (str_starts_with($id, 'Austral\\EntityBundle\\') || str_starts_with($id, 'austral.entity'))
        {
          $definition->setPublic(true);
        }
      }
    }
    $container->compile();

    $metadataFactory = $this->createStub(ClassMetadataFactory::class);
    $metadataFactory->method('getAllMetadata')->willReturn([]);
    $doctrineEntityManager = $this->createStub(EntityManagerInterface::class);
    $doctrineEntityManager->method('getMetadataFactory')->willReturn($metadataFactory);
    $container->set('doctrine.orm.entity_manager', $doctrineEntityManager);

    return $container;
  }
}

/**
 * @internal
 */
final class AppSanitizer implements AustralSanitizerInterface
{
  public function getName(): string
  {
    return 'app';
  }

  public function getSanitizer(): HtmlSanitizerInterface
  {
    throw new \LogicException('Not used by the test.');
  }
}
