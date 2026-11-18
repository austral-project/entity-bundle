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

use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\Model\PaginatorResult;
use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of object
 *
 * @extends EntityManagerInterface<T>
 *
 * Austral Interface EntityManagerORM.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityManagerORMInterface extends EntityManagerInterface
{
  /**
   * @return DoctrineEntityManagerInterface
   */
  public function getDoctrineEntityManager(): DoctrineEntityManagerInterface;

  /**
   * @param string                              $id
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveById(string $id, ?callable $closure = null): ?EntityInterface;

  /**
   * @param string                              $key
   * @param string                              $value
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveByKey(string $key, string $value, ?callable $closure = null): ?EntityInterface;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveByClosure(?callable $closure = null): ?EntityInterface;

  /**
   * @param non-empty-string                    $orderByAttribute
   * @param 'ASC'|'DESC'                        $orderByType
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return Collection<int, EntityInterface<T>>|null
   *
   * @throws QueryException
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = 'ASC', ?callable $closure = null): ?Collection;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return Collection<int, EntityInterface<T>>|null
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): ?Collection;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   * @param non-empty-string                    $alias
   *
   * @return Collection<int, EntityInterface<T>>|null
   */
  public function selectByClosure(?callable $closure, string $alias = 'root'): ?Collection;

  /**
   * @param EntityInterface<T>                  $object
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return array<string|int, mixed>
   */
  public function selectArrayLanguages(EntityInterface $object, ?callable $closure = null): array;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   * @param non-empty-string                    $alias
   *
   * @return bool
   *
   * @throws QueryException
   */
  public function deleteByClosure(?callable $closure, string $alias = 'root'): bool;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return PaginatorResult<T>
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): PaginatorResult;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   * @param non-empty-string                    $alias
   *
   * @return PaginatorResult<T>
   */
  public function paginatorByClosure(?callable $closure, string $alias = 'root'): PaginatorResult;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return int
   *
   * @throws NonUniqueResultException|NoResultException
   */
  public function countAll(?callable $closure = null): int;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return int
   *
   * @throws NoResultException|NonUniqueResultException
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int;

  /**
   * @param string                 $name
   * @param AustralQueryBuilder<T> $queryBuilder
   *
   * @return AustralQueryBuilder<T>
   */
  public function queryBuilderExtends(string $name, AustralQueryBuilder $queryBuilder): AustralQueryBuilder;

  /**
   * @param EntityInterface<T> $object
   * @param bool               $andFlush
   * @param bool               $dispatchToFlush
   *
   * @return $this
   */
  public function update(EntityInterface $object, bool $andFlush = true, bool $dispatchToFlush = false): self;
}
