<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Model;
use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Class PaginatorResult
 * @template T of object
 *
 * @package Austral\EntityBundle\Model
 *
 * @author  Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class PaginatorResult
{

  /**
   * @var Paginator<T>|null
   */
  protected ?Paginator $paginator = null;

  /**
   * @var \Exception|null
   */
  protected ?\Exception $exception;

  /**
   * @var array<string|int, object>
   */
  protected array $objects = [];

  /**
   * PaginatorResult constructor
   *
   * @param Paginator<T>|null $paginator
   * @param \Exception|null $exception
   */
  public function __construct(?Paginator $paginator = null, ?\Exception $exception = null)
  {
    $this->paginator = $paginator;
    $this->exception = $exception;
    try {
      $it = $paginator?->getIterator();
      if ($it instanceof \ArrayIterator) {
        /** @var array<string|int, object> $objects */
        $objects = $it->getArrayCopy();
      } elseif($it) {
        /** @var array<string|int, object> $objects */
        $objects = iterator_to_array($it, false);
        $this->objects = $objects;
      }
    } catch (\Exception $exception) {
      $this->exception = $exception;
    }
  }

  /**
   * getPaginator
   *
   * @return Paginator<T>|null
   */
  public function getPaginator(): ?Paginator
  {
    return $this->paginator;
  }

  /**
   * @param Paginator<T>|null $paginator
   * @return $this
   */
  public function setPaginator(?Paginator $paginator): PaginatorResult
  {
    $this->paginator = $paginator;
    return $this;
  }

  /**
   * getCount
   *
   * @return int
   */
  public function getCount(): int
  {
    return $this->paginator?->count() ?: 0;
  }

  /**
   * getObjects
   *
   * @return array<string|int, object>
   */
  public function getObjects(): array
  {
    return $this->objects;
  }

  /**
   * @param array<string|int, object> $objects
   * @return $this
   */
  public function setObjects(array $objects): PaginatorResult
  {
    $this->objects = $objects;
    return $this;
  }

  /**
   * @return object|null
   */
  public function first(): ?object
  {
    return AustralTools::first($this->getObjects());
  }

}