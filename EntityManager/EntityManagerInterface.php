<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityManager;


use Austral\EntityBundle\Event\EntityManagerEvent;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral Interface EntityManager.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityManagerInterface
{

  /**
   * @return class-string<T>|null
   */
  public function getClass(): ?string;

  /**
   * @param class-string<T> $class
   *
   * @return $this
   */
  public function setClass(string $class): self;

  /**
   * @param T $event
   * @param string $eventName
   */
  public function dispatch(object $event, string $eventName): void;

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   *
   * @return $this
   */
  public function addEntityManagerEvent(EntityManagerEvent $entityManagerEvent): self;

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMappingAll(): array;

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMappingWithAssociation(): array;

  /**
   * @param string|null $class
   *
   * @return array<string, mixed>
   */
  public function getFieldsMapping(?string $class = null): array;

  /**
   * @param string $alias
   * @param string|null $indexBy
   *
   * @return QueryBuilder|null
   */
  public function createQueryBuilder(string $alias = "root", ?string $indexBy = null): ?QueryBuilder;


}
