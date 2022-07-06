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

use Austral\EntityBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Austral Abstract Repository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
class EntityRepository extends BaseEntityRepository implements RepositoryInterface
{

  /**
   * @var string|null
   */
  protected ?string $currentLanguage = null;

  /**
   * @return bool
   */
  public function isPgsql(): bool
  {
    return ($this->_em->getConnection()->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\PgSQL\Driver);
  }

  /**
   * @param string $id
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveById(string $id, \Closure $closure = null)
  {
    return $this->retreiveByKey("id", $id, $closure);
  }

  /**
   * @param string $key
   * @param string $value
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveByKey(string $key, string $value, \Closure $closure = null)
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->where("root.{$key} = :{$key}")
      ->setParameter("{$key}", $value);

    $queryBuilder = $this->queryBuilderExtends("retreive-by-key", $queryBuilder);
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try {
      $object = $query->getSingleResult();
    } catch (NoResultException $e) {
      $object = null;
    }
    return $object;
  }

  /**
   * @param string $orderByAttribute
   * @param string $orderByType
   * @param \Closure|null $closure
   *
   * @return ArrayCollection|array
   * @throws \Doctrine\ORM\Query\QueryException
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = "ASC", \Closure $closure = null)
  {
    $queryBuilder = $this->createQueryBuilder('root');
    if(strpos($orderByAttribute, ".") === false)
    {
      $orderByAttribute = "root.".$orderByAttribute;
    }
    $queryBuilder = $this->queryBuilderExtends("select-all", $queryBuilder);
    $queryBuilder->orderBy($orderByAttribute, $orderByType);
    $queryBuilder->indexBy("root", "root.id");
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try {
      $objects = $query->execute();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param \Closure|null $closure
   *
   * @return int
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function countAll(\Closure $closure = null): int
  {
    $queryBuilder = $this->createQueryBuilder('root')
      ->select("COUNT(DISTINCT(root.id))");
    $queryBuilder = $this->queryBuilderExtends("count-all", $queryBuilder);
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    return $queryBuilder
      ->getQuery()
      ->getSingleScalarResult();
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return ArrayCollection|array
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder)
  {
    try {
      $objects = $queryBuilder->getQuery()->execute();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return ArrayCollection|array
   */
  public function selectByClosure(\Closure $closure, string $alias = "root")
  {
    $queryBuilder = $this->createQueryBuilder('root');
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    try {
      $objects = $queryBuilder->getQuery()->execute();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return array
   * @throws \Exception
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): array
  {
    try {
      $paginator = new Paginator($queryBuilder->getQuery(), true);
      $return = array(
        "count"     =>  $paginator->count(),
        "objects"   =>  $paginator->getIterator()->getArrayCopy()
      );
    } catch (NoResultException $e) {
      $return = array(
        "count"     =>  0,
        "objects"   =>  array()
      );
    }
    return $return;
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return int
   * @throws NonUniqueResultException
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int
  {
    try {
      $objects = $queryBuilder->select("COUNT(root)")->getQuery()->getSingleScalarResult();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

  /**
   * @param $name
   * @param QueryBuilder $queryBuilder
   *
   * @return QueryBuilder
   */
  public function queryBuilderExtends($name, QueryBuilder $queryBuilder): QueryBuilder
  {
    return $queryBuilder;
  }

  /**
   * @param EntityInterface $object
   * @param \Closure|null $closure
   *
   * @return array
   */
  public function selectArrayLanguages(EntityInterface $object, \Closure $closure = null): array
  {
    $queryBuilder = $this->createQueryBuilder('root');
    $queryBuilder->where("root.id = :id")
      ->setParameter("id", $object->getId())
      ->leftJoin("root.translates", "translates")
      ->select("translates.language");
    if($closure instanceof \Closure)
    {
      $closure->call($this, $queryBuilder);
    }
    $query = $queryBuilder->getQuery();
    try {
      $objects = $query->getArrayResult();
    } catch (NoResultException $e) {
      $objects = array();
    }
    return $objects;
  }

}
