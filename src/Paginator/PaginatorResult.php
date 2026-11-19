<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Paginator;

use Austral\ToolsBundle\AustralTools;

/**
 * Result of a paginated query: the objects of the requested window and the total count.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class PaginatorResult
{
  /**
   * @param array<string|int, T> $objects   objects of the requested window
   * @param int                  $count     total number of matching objects, ignoring the window
   * @param \Exception|null      $exception exception raised while running the query, if any
   */
  public function __construct(
    protected array $objects = [],
    protected int $count = 0,
    protected ?\Exception $exception = null,
  ) {
  }

  /**
   * getCount
   *
   * @return int
   */
  public function getCount(): int
  {
    return $this->count;
  }

  /**
   * getObjects
   *
   * @return array<string|int, T>
   */
  public function getObjects(): array
  {
    return $this->objects;
  }

  /**
   * @param array<string|int, T> $objects
   *
   * @return $this
   */
  public function setObjects(array $objects): PaginatorResult
  {
    $this->objects = $objects;

    return $this;
  }

  /**
   * @return T|null
   */
  public function first(): ?object
  {
    return AustralTools::first($this->getObjects());
  }

  /**
   * getException
   *
   * @return \Exception|null
   */
  public function getException(): ?\Exception
  {
    return $this->exception;
  }
}
