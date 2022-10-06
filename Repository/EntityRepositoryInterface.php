<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Interface Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityRepositoryInterface
{

  /**
   * @param EventDispatcherInterface|null $dispatcher
   *
   * @return EntityRepository
   */
  public function setDispatcher(?EventDispatcherInterface $dispatcher): EntityRepository;

  /**
   * @return bool
   */
  public function isPgsql(): bool;

  /**
   * @param string $id
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveById(string $id, \Closure $closure = null);

  /**
   * @param string $key
   * @param string $value
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveByKey(string $key, string $value, \Closure $closure = null);

  /**
   * @param string $orderByAttribute
   * @param string $orderByType
   * @param \Closure|null $closure
   *
   * @return ArrayCollection|array
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = "ASC", \Closure $closure = null);

  /**
   * @return int
   * @param \Closure|null $closure
   * @throws NonUniqueResultException|NoResultException
   */
  public function countAll(\Closure $closure = null): int;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return ArrayCollection|array
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder);

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return ArrayCollection|array
   */
  public function selectByClosure(\Closure $closure, string $alias = "root");

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return array
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): array;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return int
   * @throws NoResultException|NonUniqueResultException
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int;

  /**
   * @param $name
   * @param QueryBuilder $queryBuilder
   *
   * @return QueryBuilder
   */
  public function queryBuilderExtends($name, QueryBuilder $queryBuilder): QueryBuilder;


}
