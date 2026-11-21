<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Mapping\ValueAccessor;

use Austral\EntityBundle\Mapping\ValueAccessor\EntityValueAccessor;
use Austral\EntityBundle\Tests\Fixtures\DuplicableEntity;
use PHPUnit\Framework\TestCase;

final class EntityValueAccessorTest extends TestCase
{
  public function testGetValueReadsTheEntity(): void
  {
    $accessor = new EntityValueAccessor();

    self::assertSame('Home', $accessor->getValue(new DuplicableEntity(), 'title'));
  }

  public function testSetValueWritesTheEntity(): void
  {
    $entity = new DuplicableEntity();
    new EntityValueAccessor()->setValue($entity, 'title', 'About');

    self::assertSame('About', $entity->getTitle());
  }

  public function testUnknownFieldIsIgnored(): void
  {
    $entity = new DuplicableEntity();
    $accessor = new EntityValueAccessor();
    $accessor->setValue($entity, 'unknown', 'value');

    self::assertNull($accessor->getValue($entity, 'unknown'));
  }
}
