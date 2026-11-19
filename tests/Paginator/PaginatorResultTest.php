<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Paginator;

use Austral\EntityBundle\Paginator\PaginatorResult;
use PHPUnit\Framework\TestCase;

final class PaginatorResultTest extends TestCase
{
  public function testObjectsComeFromThePaginator(): void
  {
    $first = new \stdClass();
    $second = new \stdClass();

    $result = new PaginatorResult([$first, $second], 42);

    self::assertSame([$first, $second], $result->getObjects());
    self::assertSame($first, $result->first());
    self::assertSame(42, $result->getCount());
    self::assertNull($result->getException());
  }

  public function testWithoutPaginator(): void
  {
    $exception = new \RuntimeException('Repository is not defined !');
    /** @var array<int, \stdClass> $objects */
    $objects = [];
    $result = new PaginatorResult($objects, 0, $exception);

    self::assertSame($objects, $result->getObjects());
    self::assertNull($result->first());
    self::assertSame(0, $result->getCount());
    self::assertSame($exception, $result->getException());
  }
}
