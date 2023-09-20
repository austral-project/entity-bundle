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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * Austral Interface EntityManagerORM.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityManagerORMInterface extends EntityManagerInterface
{

  /**
   * @return array
   */
  public function getFieldsMappingAll(): array;

  /**
   * @return array
   */
  public function getFieldsMappingWithAssociation(): array;

  /**
   * @param null $class
   *
   * @return array
   */
  public function getFieldsMapping($class = null): array;

  /**
   * @return DoctrineEntityManagerInterface
   */
  public function getDoctrineEntityManager(): DoctrineEntityManagerInterface;

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
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveByClosure(\Closure $closure = null);

  /**
   * @param string $orderByAttribute
   * @param string $orderByType
   * @param \Closure|null $closure
   *
   * @return ArrayCollection|array
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = "ASC", \Closure $closure = null): array;

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
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): array;

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return ArrayCollection|array
   */
  public function selectByClosure(\Closure $closure, string $alias = "root"): array;

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return bool
   */
  public function deleteByClosure(\Closure $closure, string $alias = "root"): bool;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return array
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): array;

  /**
   * @param \Closure $closure
   * @param string $alias
   * @return array
   */
  public function paginatorByClosure(\Closure $closure, string $alias = "root"): array;



}
