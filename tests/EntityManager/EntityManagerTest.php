<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\EntityManager;

use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Repository\EntityRepository;
use Austral\EntityBundle\Tests\Fixtures\DuplicableEntity;
use Austral\EntityBundle\Tests\Fixtures\TimestampableObject;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class EntityManagerTest extends TestCase
{
  /**
   * @var list<string>
   */
  private array $dispatched = [];

  private EventDispatcher $dispatcher;

  /**
   * @var DoctrineEntityManagerInterface&MockObject
   */
  private DoctrineEntityManagerInterface $doctrine;

  protected function setUp(): void
  {
    $this->dispatched = [];
    $this->dispatcher = new EventDispatcher();
    foreach ([EntityManagerEvent::EVENT_PUSH_BEFORE, EntityManagerEvent::EVENT_PUSH_AFTER] as $eventName)
    {
      $this->dispatcher->addListener($eventName, function (EntityManagerEvent $event) use ($eventName): void {
        $this->dispatched[] = "{$eventName}:{$event->getName()}";
      });
    }
    $this->doctrine = $this->createMock(DoctrineEntityManagerInterface::class);
  }

  public function testFlushDispatchesPendingEventsOnlyOnce(): void
  {
    $this->doctrine->expects(self::exactly(2))->method('flush');
    $entityManager = new EntityManager($this->doctrine, $this->dispatcher);
    $event = new EntityManagerEvent('update', $entityManager, new DuplicableEntity());
    $event->setDispatchToFlush(true);
    $entityManager->addEntityManagerEvent($event);

    $entityManager->flush();
    $entityManager->flush();

    self::assertSame([
      EntityManagerEvent::EVENT_PUSH_BEFORE.':update',
      EntityManagerEvent::EVENT_PUSH_AFTER.':update',
    ], $this->dispatched);
  }

  public function testFlushIgnoresEventsNotMarkedForFlush(): void
  {
    $entityManager = new EntityManager($this->doctrine, $this->dispatcher);
    $entityManager->addEntityManagerEvent(new EntityManagerEvent('update', $entityManager, new DuplicableEntity()));

    $entityManager->flush();

    self::assertSame([], $this->dispatched);
  }

  public function testDeletesFlushesOnceAndDispatchesPushEvents(): void
  {
    $this->doctrine->expects(self::exactly(2))->method('remove');
    $this->doctrine->expects(self::once())->method('flush');
    $entityManager = new EntityManager($this->doctrine, $this->dispatcher);

    $entityManager->deletes([new DuplicableEntity(), new DuplicableEntity()]);

    self::assertSame([
      EntityManagerEvent::EVENT_PUSH_BEFORE.':delete',
      EntityManagerEvent::EVENT_PUSH_BEFORE.':delete',
      EntityManagerEvent::EVENT_PUSH_AFTER.':delete',
      EntityManagerEvent::EVENT_PUSH_AFTER.':delete',
    ], $this->dispatched);
  }

  public function testGetRepositoryForAnotherClassKeepsCurrentRepository(): void
  {
    $currentRepository = $this->createMock(EntityRepository::class);
    $currentRepository->expects(self::once())->method('setDispatcher')->with($this->dispatcher);
    $otherRepository = $this->createMock(EntityRepository::class);
    $otherRepository->expects(self::once())->method('setDispatcher')->with($this->dispatcher);
    $this->doctrine->method('getRepository')->willReturnMap([
      [DuplicableEntity::class, $currentRepository],
      [TimestampableObject::class, $otherRepository],
    ]);
    $this->doctrine->method('getClassMetadata')->willReturn(new ClassMetadata(DuplicableEntity::class));
    $entityManager = new EntityManager($this->doctrine, $this->dispatcher);
    $entityManager->setClass(DuplicableEntity::class);

    self::assertSame($otherRepository, $entityManager->getRepository(TimestampableObject::class));
    self::assertSame($currentRepository, $entityManager->getRepository());
  }

  public function testSelectMethodsReturnAnEmptyArrayWithoutRepository(): void
  {
    $entityManager = new EntityManager($this->doctrine, $this->dispatcher);

    self::assertSame([], $entityManager->selectAll());
    self::assertSame([], $entityManager->selectByClosure(null));
    self::assertSame([], $entityManager->selectByQueryBuilder($this->createMock(QueryBuilder::class)));
  }
}
