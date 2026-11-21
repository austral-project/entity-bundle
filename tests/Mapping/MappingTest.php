<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Mapping;

use Austral\EntityBundle\EntityAttributes\EntitiesAttributes;
use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\EntityBundle\Tests\Fixtures\DuplicableEntity;
use Austral\EntityBundle\Tests\Fixtures\TimestampableObject;
use Austral\ToolsBundle\Services\Debug;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class MappingTest extends TestCase
{
  /** @var class-string */
  private const string PAGE = DuplicableEntity::class;

  /** @var class-string */
  private const string RUNTIME = TimestampableObject::class;

  /** @var class-string */
  private const string PARTIAL = \stdClass::class;

  private EventDispatcher $dispatcher;

  private int $entitiesAttributesCalls = 0;

  private int $events = 0;

  protected function setUp(): void
  {
    $this->dispatcher = new EventDispatcher();
    $this->entitiesAttributesCalls = 0;
    $this->events = 0;
    $this->dispatcher->addListener(EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING, function (): void {
      ++$this->events;
    }, 4096);
  }

  public function testMappingIsNotBuiltWhenTheServiceIsCreated(): void
  {
    $this->createMapping();

    self::assertSame(0, $this->entitiesAttributesCalls);
    self::assertSame(0, $this->events);
  }

  public function testMappingIsBuiltOnceOnFirstAccess(): void
  {
    $this->addMappingListener(self::PAGE, 'page');
    $mapping = $this->createMapping();

    self::assertInstanceOf(EntityMapping::class, $mapping->getEntityMapping(self::PAGE));
    self::assertCount(1, $mapping->getEntitiesMapping());
    self::assertSame('page', $mapping->getSlugger(self::PAGE));
    self::assertSame(1, $this->events);
    self::assertSame(1, $this->entitiesAttributesCalls);
  }

  public function testEntityMappingCanBeFoundBySlugger(): void
  {
    $this->addMappingListener(self::PAGE, 'page');
    $mapping = $this->createMapping();

    self::assertSame(self::PAGE, $mapping->getEntityMapping('page')?->entityClass);
  }

  public function testListenersCanReadAndFillTheMappingWhileItIsBuilt(): void
  {
    $seenDuringBuild = 'not called';
    $this->dispatcher->addListener(EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING, function (EntityMappingEvent $event) use (&$seenDuringBuild): void {
      $seenDuringBuild = $event->getMapping()->getEntityMapping(self::PAGE);
      $event->getMapping()->addEntityMapping(self::PAGE, self::entityMapping(self::PAGE, 'page'));
      $event->getMapping()->getEntitiesMapping();
    });
    $mapping = $this->createMapping();

    self::assertNotNull($mapping->getEntityMapping(self::PAGE));
    self::assertNull($seenDuringBuild);
    self::assertSame(1, $this->events);
  }

  public function testMappingAddedAfterTheBuildIsKeptWithTheBuiltOnes(): void
  {
    $this->addMappingListener(self::PAGE, 'page');
    $mapping = $this->createMapping();

    $mapping->addEntityMapping(self::RUNTIME, self::entityMapping(self::RUNTIME, 'runtime'));

    self::assertNotNull($mapping->getEntityMapping(self::PAGE));
    self::assertNotNull($mapping->getEntityMapping(self::RUNTIME));
    self::assertSame(1, $this->events);
  }

  public function testFailedBuildIsDiscardedAndBuiltAgainOnNextAccess(): void
  {
    $fail = true;
    $this->dispatcher->addListener(EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING, function (EntityMappingEvent $event) use (&$fail): void {
      $event->getMapping()->addEntityMapping(self::PARTIAL, self::entityMapping(self::PARTIAL, 'partial'));
      if ($fail)
      {
        $fail = false;
        throw new \RuntimeException('Build failed');
      }
      $event->getMapping()->addEntityMapping(self::PAGE, self::entityMapping(self::PAGE, 'page'));
    });
    $mapping = $this->createMapping();

    try
    {
      $mapping->getEntitiesMapping();
      self::fail('The build exception must not be swallowed.');
    }
    catch (\RuntimeException $exception)
    {
      self::assertSame('Build failed', $exception->getMessage());
    }

    self::assertSame([self::PARTIAL, self::PAGE], array_keys($mapping->getEntitiesMapping()));
    self::assertSame(2, $this->events);
  }

  /**
   * @return Mapping<object>
   */
  private function createMapping(): Mapping
  {
    $entitiesAttributes = $this->createStub(EntitiesAttributes::class);

    return new Mapping(
      $this->createStub(EntityManager::class),
      new Debug(),
      $this->dispatcher,
      function () use ($entitiesAttributes): EntitiesAttributes {
        ++$this->entitiesAttributesCalls;

        return $entitiesAttributes;
      },
    );
  }

  /**
   * @param class-string $entityClass
   *
   * @return EntityMapping<object>
   */
  private static function entityMapping(string $entityClass, string $slugger): EntityMapping
  {
    return new EntityMapping($entityClass, $slugger);
  }

  /**
   * @param class-string $entityClass
   */
  private function addMappingListener(string $entityClass, string $slugger): void
  {
    $this->dispatcher->addListener(EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING, static function (EntityMappingEvent $event) use ($entityClass, $slugger): void {
      $event->getMapping()->addEntityMapping($entityClass, self::entityMapping($entityClass, $slugger));
    });
  }
}
