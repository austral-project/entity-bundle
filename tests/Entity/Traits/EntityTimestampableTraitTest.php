<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Entity\Traits;

use Austral\EntityBundle\Tests\Fixtures\TimestampableObject;
use PHPUnit\Framework\TestCase;

final class EntityTimestampableTraitTest extends TestCase
{
  public function testSettersAndGetters(): void
  {
    $created = new \DateTime('2026-01-01 10:00:00');
    $updated = new \DateTime('2026-02-01 12:00:00');

    $object = new TimestampableObject();
    self::assertSame($object, $object->setCreated($created));
    self::assertSame($object, $object->setUpdated($updated));

    self::assertSame($created, $object->getCreated());
    self::assertSame($updated, $object->getUpdated());
  }

  public function testGettersReturnADateWhenNothingIsSet(): void
  {
    $object = new TimestampableObject();

    self::assertInstanceOf(\DateTime::class, $object->getCreated());
    self::assertInstanceOf(\DateTime::class, $object->getUpdated());
  }
}
