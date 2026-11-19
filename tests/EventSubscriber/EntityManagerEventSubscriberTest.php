<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\EventSubscriber;

use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\EventSubscriber\EntityManagerEventSubscriber;
use Austral\EntityBundle\Tests\Fixtures\DuplicableEntity;
use Doctrine\ORM\Mapping\FieldMapping;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class EntityManagerEventSubscriberTest extends TestCase
{
  public function testDuplicateRegeneratesStringIdentifier(): void
  {
    $object = new DuplicableEntity();
    $object->setId('source-id');

    new EntityManagerEventSubscriber()->duplicate($this->createEvent($object, self::fieldsMapping()));

    self::assertNotSame('source-id', $object->getId());
    self::assertTrue(Uuid::isValid($object->getId()));
  }

  public function testDuplicateSuffixesUniqueStringFieldsOnly(): void
  {
    $object = new DuplicableEntity();

    new EntityManagerEventSubscriber()->duplicate($this->createEvent($object, self::fieldsMapping()));

    self::assertMatchesRegularExpression('/^home - [a-z0-9]{8}$/', $object->getKeyname());
    self::assertSame('Home', $object->getTitle());
    self::assertSame(1, $object->getPosition());
  }

  public function testDuplicateLeavesNonStringIdentifierUntouched(): void
  {
    $id = new FieldMapping('integer', 'id', 'id');
    $id->id = true;
    $object = new DuplicableEntity();
    $object->setId('source-id');

    new EntityManagerEventSubscriber()->duplicate($this->createEvent($object, ['id' => $id]));

    self::assertSame('source-id', $object->getId());
  }

  public function testDuplicateIgnoresEntriesThatAreNotFieldMappings(): void
  {
    $object = new DuplicableEntity();
    $object->setId('source-id');

    new EntityManagerEventSubscriber()->duplicate($this->createEvent($object, ['id' => ['type' => 'string', 'id' => true]]));

    self::assertSame('source-id', $object->getId());
  }

  /**
   * @return array<string, FieldMapping>
   */
  private static function fieldsMapping(): array
  {
    $id = new FieldMapping('string', 'id', 'id');
    $id->id = true;
    $keyname = new FieldMapping('string', 'keyname', 'keyname');
    $keyname->unique = true;
    $position = new FieldMapping('integer', 'position', 'position');
    $position->unique = true;

    return [
      'id' => $id,
      'keyname' => $keyname,
      'title' => new FieldMapping('string', 'title', 'title'),
      'position' => $position,
    ];
  }

  /**
   * @param array<string, mixed> $fieldsMapping
   *
   * @return EntityManagerEvent<object>
   */
  private function createEvent(DuplicableEntity $object, array $fieldsMapping): EntityManagerEvent
  {
    $entityManager = $this->createMock(EntityManager::class);
    $entityManager->method('getFieldsMapping')
      ->with(DuplicableEntity::class)
      ->willReturn($fieldsMapping);

    return new EntityManagerEvent('duplicate', $entityManager, $object);
  }
}
