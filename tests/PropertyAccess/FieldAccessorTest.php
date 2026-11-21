<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\PropertyAccess;

use Austral\EntityBundle\PropertyAccess\FieldAccessor;
use Austral\EntityBundle\Tests\Fixtures\FieldAccessorObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

final class FieldAccessorTest extends TestCase
{
  /**
   * @return iterable<string, array{string, bool}>
   */
  public static function readableProvider(): iterable
  {
    yield 'getter' => ['title', true];
    yield 'is prefix' => ['enabled', true];
    yield 'has prefix' => ['children', true];
    yield 'method named after the field' => ['label', true];
    yield 'public property' => ['publicField', true];
    yield 'protected property without getter' => ['hidden', false];
    yield 'only reachable through __get' => ['unknown', false];
  }

  #[DataProvider('readableProvider')]
  public function testIsReadableOnAnObjectAndOnItsClass(string $fieldname, bool $expected): void
  {
    self::assertSame($expected, FieldAccessor::isReadable(new FieldAccessorObject(), $fieldname));
    self::assertSame($expected, FieldAccessor::isReadable(FieldAccessorObject::class, $fieldname));
  }

  /**
   * @return iterable<string, array{string, bool}>
   */
  public static function writableProvider(): iterable
  {
    yield 'setter' => ['title', true];
    yield 'adder and remover' => ['tags', true];
    yield 'public property' => ['publicField', true];
    yield 'getter without setter' => ['enabled', false];
    yield 'only reachable through __set' => ['unknown', false];
  }

  #[DataProvider('writableProvider')]
  public function testIsWritableOnAnObjectAndOnItsClass(string $fieldname, bool $expected): void
  {
    self::assertSame($expected, FieldAccessor::isWritable(new FieldAccessorObject(), $fieldname));
    self::assertSame($expected, FieldAccessor::isWritable(FieldAccessorObject::class, $fieldname));
  }

  public function testGetValue(): void
  {
    $object = new FieldAccessorObject();

    self::assertSame('Home', FieldAccessor::getValue($object, 'title'));
    self::assertTrue(FieldAccessor::getValue($object, 'enabled'));
    self::assertSame('public', FieldAccessor::getValue($object, 'publicField'));
  }

  public function testSetValueUsesSettersAndAdders(): void
  {
    $object = new FieldAccessorObject();
    FieldAccessor::setValue($object, 'title', 'About');
    FieldAccessor::setValue($object, 'tags', ['news', 'event']);

    self::assertSame('About', $object->getTitle());
    self::assertSame(['news', 'event'], $object->getTags());
  }

  public function testGetValueNeverFallsBackOnGet(): void
  {
    $this->expectException(NoSuchPropertyException::class);

    FieldAccessor::getValue(new FieldAccessorObject(), 'unknown');
  }

  public function testSetValueNeverFallsBackOnSet(): void
  {
    $object = new FieldAccessorObject();

    try
    {
      FieldAccessor::setValue($object, 'unknown', 'value');
      self::fail('A NoSuchPropertyException was expected.');
    }
    catch (NoSuchPropertyException)
    {
      self::assertSame('magic', $object->__get('unknown'), '__set must not have been called.');
    }
  }
}
