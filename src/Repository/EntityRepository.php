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

use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\Model\PaginatorResult;
use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Abstract Repository.
 *
 * @template T of object
 *
 * @extends \Doctrine\ORM\EntityRepository<T>
 *
 * @implements EntityRepositoryInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class EntityRepository extends \Doctrine\ORM\EntityRepository implements EntityRepositoryInterface
{
  /** @var EventDispatcherInterface|null */
  private ?EventDispatcherInterface $dispatcher = null;

  /**
   * @param string      $alias
   * @param string|null $indexBy
   *
   * @return AustralQueryBuilder<T>
   */
  public function createQueryBuilder(string $alias, ?string $indexBy = null): AustralQueryBuilder
  {
    /** @var class-string $entityName */
    $entityName = $this->getEntityName();

    /** @var AustralQueryBuilder<T> $australQueryBuilder */
    $australQueryBuilder = new AustralQueryBuilder($this->getEntityManager(), $this->getClassName(), $this->dispatcher);
    $australQueryBuilder->select($alias);
    $australQueryBuilder->from($entityName, $alias, $indexBy);

    return $australQueryBuilder;
  }

  /**
   * @param EventDispatcherInterface|null $dispatcher
   *
   * @return $this
   */
  public function setDispatcher(?EventDispatcherInterface $dispatcher): self
  {
    if (!$this->dispatcher)
    {
      $this->dispatcher = $dispatcher;
    }

    return $this;
  }

  /**
   * @return bool
   */
  public function isPgsql(): bool
  {
    return $this->getEntityManager()->getConnection()->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveById(string $id, ?callable $closure = null): ?EntityInterface
  {
    return $this->retrieveByKey('id', $id, $closure);
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveByKey(string $key, string $value, ?callable $closure = null): ?EntityInterface
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.{$key} = :{$key}")
      ->setParameter("{$key}", $value);

    $queryBuilder = $this->queryBuilderExtends('retrieve-by-key', $queryBuilder);
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try
    {
      $object = $query->getSingleResult();
    }
    catch (NoResultException $e)
    {
      $object = null;
    }

    return $object;
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveByClosure(?callable $closure = null): ?EntityInterface
  {
    $queryBuilder = $this->createQueryBuilder('root');
    $queryBuilder = $this->queryBuilderExtends('retrieve-by-closure', $queryBuilder);
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $queryBuilder->setMaxResults(1);
    $query = $queryBuilder->getQuery();
    try
    {
      $object = $query->getSingleResult();
    }
    catch (NoResultException $e)
    {
      $object = null;
    }

    return $object;
  }

  /**
   * {@inheritDoc}
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = 'ASC', ?callable $closure = null): ?Collection
  {
    $queryBuilder = $this->createQueryBuilder('root');
    if (!str_contains($orderByAttribute, '.'))
    {
      $orderByAttribute = 'root.'.$orderByAttribute;
    }
    $queryBuilder = $this->queryBuilderExtends('select-all', $queryBuilder);
    $queryBuilder->orderBy($orderByAttribute, $orderByType);
    $queryBuilder->indexBy('root', 'root.id');
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try
    {
      $objects = $query->execute();
    }
    catch (NoResultException $e)
    {
      $objects = [];
    }

    return $objects;
  }

  /**
   * {@inheritDoc}
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): ?Collection
  {
    try
    {
      $objects = $queryBuilder->getQuery()->execute();
    }
    catch (NoResultException $e)
    {
      $objects = new ArrayCollection();
    }

    return $objects;
  }

  /**
   * {@inheritDoc}
   */
  public function selectByClosure(?callable $closure, string $alias = 'root'): ?Collection
  {
    $queryBuilder = $this->createQueryBuilder($alias);
    $queryBuilder = $this->queryBuilderExtends('select-by-closure', $queryBuilder);
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }

    return $this->selectByQueryBuilder($queryBuilder);
  }

  /**
   * {@inheritDoc}
   */
  public function deleteByClosure(?callable $closure, string $alias = 'root'): bool
  {
    $queryBuilder = $this->createQueryBuilder($alias);
    if ($closure instanceof \Closure)
    {
      $queryBuilderReturn = $closure->call($this, $queryBuilder);
      if ($queryBuilderReturn)
      {
        $queryBuilder = $queryBuilderReturn;
      }
    }
    $queryBuilderIds = clone $queryBuilder;

    $ids = $queryBuilderIds->select('root.id')
      ->indexBy('root', 'root.id')
      ->getQuery()
      ->getArrayResult();

    if ($ids)
    {
      $queryBuilderDelete = $this->createQueryBuilder($alias);
      $queryBuilderDelete->where('root.id in (:ids)')
        ->setParameter('ids', array_keys($ids))
        ->delete($queryBuilder->getEntityClassname(), $alias)
        ->getQuery()
        ->execute();
    }

    return true;
  }

  /**
   * {@inheritDoc}
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): PaginatorResult
  {
    try
    {
      $paginator = new Paginator($queryBuilder->getQuery(), true);
      $paginatorResult = new PaginatorResult($paginator);
    }
    catch (\Exception $e)
    {
      $paginatorResult = new PaginatorResult(null, $e);
    }

    return $paginatorResult;
  }

  /**
   * {@inheritDoc}
   */
  public function paginatorByClosure(?callable $closure, string $alias = 'root'): PaginatorResult
  {
    $queryBuilder = $this->createQueryBuilder($alias);
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }

    return $this->paginatorByQueryBuilder($queryBuilder);
  }

  /**
   * {@inheritDoc}
   */
  public function countAll(?callable $closure = null): int
  {
    $queryBuilder = $this->createQueryBuilder('root')->select('COUNT(DISTINCT(root.id))');
    $queryBuilder = $this->queryBuilderExtends('count-all', $queryBuilder);
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $count = $queryBuilder->getQuery()->getSingleScalarResult();

    return is_integer($count) ? $count : 0;
  }

  /**
   * {@inheritDoc}
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int
  {
    $count = $queryBuilder->select('COUNT(root)')->getQuery()->getSingleScalarResult();

    return is_integer($count) ? $count : 0;
  }

  /**
   * {@inheritDoc}
   */
  public function queryBuilderExtends($name, AustralQueryBuilder $queryBuilder): AustralQueryBuilder
  {
    return $queryBuilder;
  }

  /**
   * {@inheritDoc}
   */
  public function selectArrayLanguages(EntityInterface $object, ?callable $closure = null): array
  {
    $queryBuilder = $this->createQueryBuilder('root');
    $queryBuilder->where('root.id = :id')
      ->setParameter('id', $object->getId())
      ->leftJoin('root.translates', 'translates')
      ->select('translates.language');
    if ($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try
    {
      $objects = $query->getArrayResult();
    }
    catch (NoResultException $e)
    {
      $objects = [];
    }

    return $objects;
  }
}
