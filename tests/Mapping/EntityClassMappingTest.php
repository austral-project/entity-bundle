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

use Austral\EntityBundle\Mapping\ValueAccessor\EntityValueAccessor;
use Austral\EntityBundle\Tests\Fixtures\DuplicableEntity;
use Austral\EntityBundle\Tests\Fixtures\RecordingValueAccessor;
use Austral\EntityBundle\Tests\Fixtures\TestEntityClassMapping;
use PHPUnit\Framework\TestCase;

final class EntityClassMappingTest extends TestCase
{
  public function testDefaultAccessorReadsAndWritesTheEntity(): void
  {
    $entity = new DuplicableEntity();
    $mapping = new TestEntityClassMapping();

    self::assertInstanceOf(EntityValueAccessor::class, $mapping->getValueAccessor());
    self::assertSame('Home', $mapping->getObjectValue($entity, 'title'));
    $mapping->setObjectValue($entity, 'title', 'About');
    self::assertSame('About', $entity->getTitle());
  }

  public function testCustomAccessorIsUsedForGetAndSet(): void
  {
    $entity = new DuplicableEntity();
    $accessor = new RecordingValueAccessor();
    $mapping = new TestEntityClassMapping();

    self::assertSame($mapping, $mapping->setValueAccessor($accessor));
    self::assertSame('from-accessor', $mapping->getObjectValue($entity, 'title'));
    self::assertSame($mapping, $mapping->setObjectValue($entity, 'title', 'About'));

    self::assertSame([['get', 'title', null], ['set', 'title', 'About']], $accessor->calls);
    self::assertSame('Home', $entity->getTitle(), 'The mapping must not write the entity itself.');
  }
}
