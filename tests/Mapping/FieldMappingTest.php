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
use Austral\EntityBundle\Tests\Fixtures\TestFieldMapping;
use PHPUnit\Framework\TestCase;

final class FieldMappingTest extends TestCase
{
  public function testDefaultAccessorIsTheEntityAccessor(): void
  {
    $mapping = new TestFieldMapping();

    self::assertInstanceOf(EntityValueAccessor::class, $mapping->getValueAccessor());
    self::assertSame($mapping->getValueAccessor(), $mapping->getValueAccessor());
  }

  public function testDefaultAccessorUsesTheMappingFieldname(): void
  {
    $entity = new DuplicableEntity();
    $mapping = new TestFieldMapping()->setFieldname('title');

    self::assertSame('Home', $mapping->getObjectValue($entity));
    $mapping->setObjectValue($entity, null, 'About');
    self::assertSame('About', $entity->getTitle());
  }

  public function testExplicitFieldnameOverridesTheMappingFieldname(): void
  {
    $mapping = new TestFieldMapping()->setFieldname('title');

    self::assertSame('home', $mapping->getObjectValue(new DuplicableEntity(), 'keyname'));
  }

  public function testWithoutFieldnameNothingIsCalled(): void
  {
    $accessor = new RecordingValueAccessor();
    $mapping = new TestFieldMapping()->setValueAccessor($accessor);

    self::assertNull($mapping->getObjectValue(new DuplicableEntity()));
    $mapping->setObjectValue(new DuplicableEntity(), null, 'About');
    self::assertSame([], $accessor->calls);
  }

  public function testCustomAccessorIsUsedForGetAndSet(): void
  {
    $entity = new DuplicableEntity();
    $accessor = new RecordingValueAccessor();
    $mapping = new TestFieldMapping()->setFieldname('title');

    self::assertSame($mapping, $mapping->setValueAccessor($accessor));
    self::assertSame('from-accessor', $mapping->getObjectValue($entity));
    self::assertSame($mapping, $mapping->setObjectValue($entity, null, 'About'));

    self::assertSame([['get', 'title', null], ['set', 'title', 'About']], $accessor->calls);
    self::assertSame('Home', $entity->getTitle(), 'The mapping must not write the entity itself.');
  }
}
