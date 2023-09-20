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
use Austral\EntityBundle\Event\EntityManagerMappingEvent;
use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Austral Abstract EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class EntityManager implements EntityManagerInterface, EntityManagerORMInterface
{

  /**
   * @var DoctrineEntityManagerInterface
   */
  protected DoctrineEntityManagerInterface $em;

  /**
   * @var ?EntityRepositoryInterface
   */
  protected $repository = null;

  /**
   * @var string|null
   */
  protected ?string $class = null;

  /**
   * @var string|null
   */
  protected ?string $currentLanguage = null;

  /**
   * @var string|UserInterface
   */
  protected $user;

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $dispatcher;

  /**
   * @var array
   */
  protected array $entityManagerEvents = array();

  /**
   * Constructor.
   *
   * @param DoctrineEntityManagerInterface $em
   * @param EventDispatcherInterface $dispatcher
   * @param string|null $class
   */
  public function __construct(DoctrineEntityManagerInterface $em, EventDispatcherInterface $dispatcher, ?string $class = null)
  {
    $this->em = $em;
    if($class)
    {
      $this->repository = $em->getRepository($class);
      $this->class = $em->getClassMetadata($class)->getName();
    }
    $this->dispatcher = $dispatcher;
  }

  /**
   * @return bool
   */
  public function isPgsql(): bool
  {
    return ($this->getDoctrineEntityManager()->getConnection()->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\PgSQL\Driver);
  }

  /**
   * @param string $alias
   * @param null $indexBy
   *
   * @return QueryBuilder
   */
  public function createQueryBuilder(string $alias = "root", $indexBy = null): QueryBuilder
  {
    return $this->getRepository()->createQueryBuilder($alias, $indexBy);
  }

  /**
   * @param string|null $currentLanguage
   *
   * @return $this
   */
  public function setCurrentLanguage(string $currentLanguage = null): EntityManager
  {
    $this->currentLanguage = $currentLanguage;
    return $this;
  }
  
  /**
   * @param $event
   * @param string $eventName
   */
  public function dispatch($event, string $eventName)
  {
    $this->dispatcher->dispatch($event, $eventName);
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @return $this
   */
  public function addEntitytManagerEvent(EntityManagerEvent $entityManagerEvent): EntityManager
  {
    $this->entityManagerEvents[] = $entityManagerEvent;
    return $this;
  }

  /**
   * @return DoctrineEntityManagerInterface
   */
  public function getDoctrineEntityManager(): DoctrineEntityManagerInterface
  {
    return $this->em;
  }

  /**
   * @return array
   */
  public function getFieldsMappingAll(): array
  {
    return array_merge($this->getFieldsMappingWithAssociation(), $this->getFieldsMapping());
  }

  /**
   * @return array
   */
  public function getFieldsMappingWithAssociation(): array
  {
    $mappingEvent = new EntityManagerMappingEvent($this, $this->em->getClassMetadata($this->class)->associationMappings);
    $this->dispatch($mappingEvent, EntityManagerMappingEvent::EVENT_NAME);
    return $mappingEvent->getFieldsMapping();
  }

  /**
   * @param null $class
   *
   * @return array
   */
  public function getFieldsMapping($class = null): array
  {
    return $this->em->getClassMetadata($class ?? $this->class)->fieldMappings;
  }

  /**
   * @param null $class
   *
   * @return ClassMetadata
   */
  public function getClassMetadata($class = null): ClassMetadata
  {
    return $this->em->getClassMetadata($class ?? $this->class);
  }

  /**
   * @param EntityInterface $sourceObject
   *
   * @return EntityInterface
   */
  public function duplicate(EntityInterface $sourceObject): EntityInterface
  {
    /** @var EntityInterface $duplicateObject */
    $objectDuplicate = clone $sourceObject;
    $entityManagerEvent = new EntityManagerEvent("duplicate", $this, $objectDuplicate);
    $entityManagerEvent->setSourceObject($sourceObject);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->dispatch( $entityManagerEvent,EntityManagerEvent::EVENT_DUPLICATE);
    $this->addEntitytManagerEvent($entityManagerEvent);
    return $objectDuplicate;
  }

  /**
   * @param array $values
   *
   * @return mixed
   */
  public function create(array $values = array())
  {
    $class = $this->getClass();
    $object = new $class;
    $entityManagerEvent = new EntityManagerEvent("create", $this, $object);
    $entityManagerEvent->setHydrateValues($values);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->dispatch( $entityManagerEvent,EntityManagerEvent::EVENT_CREATE);
    $this->addEntitytManagerEvent($entityManagerEvent);
    return $object;
  }

  /**
   * {@inheritDoc}
   */
  public function getClass(): string
  {
    return $this->class;
  }

  /**
   * @param string $class
   *
   * @return $this
   */
  public function setClass(string $class): EntityManager
  {
    $this->repository = $this->em->getRepository($class);
    $this->class = $this->em->getClassMetadata($class)->getName();
    return $this;
  }

  /**
   * @param EntityInterface $object
   * @param bool $andFlush
   * @param bool $dispatchToFlush
   *
   * @return $this
   */
  public function update(EntityInterface $object, bool $andFlush = true, bool $dispatchToFlush = false): EntityManager
  {
    $entityManagerEvent = new EntityManagerEvent("update", $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_UPDATE);
    $this->addEntitytManagerEvent($entityManagerEvent);
    $entityManagerEvent->setDispatchToFlush($dispatchToFlush);
    $this->em->persist($object);
    if ($andFlush)
    {
      $this->flush();
    }
    return $this;
  }

  /**
   * @param EntityInterface $object
   * @param bool $andFlush
   *
   * @return $this
   */
  public function delete(EntityInterface $object, bool $andFlush = true): EntityManager
  {
    $entityManagerEvent = new EntityManagerEvent("delete", $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_DELETE);
    $this->addEntitytManagerEvent($entityManagerEvent);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->em->remove($object);
    if ($andFlush)
    {
      $this->flush();
    }
    return $this;
  }

  /**
   * @return $this
   */
  public function flush(): EntityManager
  {
    if($this->entityManagerEvents)
    {
      /** @var EntityManagerEvent $entityManagerEvent */
      foreach ($this->entityManagerEvents as $entityManagerEvent)
      {
        if($entityManagerEvent->getDispatchToFlush())
        {
          $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_BEFORE);
        }
      }
    }
    $this->em->flush();
    if($this->entityManagerEvents)
    {
      /** @var EntityManagerEvent $entityManagerEvent */
      foreach ($this->entityManagerEvents as $entityManagerEvent)
      {
        if($entityManagerEvent->getDispatchToFlush())
        {
          $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_AFTER);
        }
      }
    }
     return $this;
  }

  /**
   * @param EntityInterface $object
   *
   * @return $this
   */
  public function refresh(EntityInterface $object): EntityManager
  {
    $this->em->refresh($object);
    return $this;
  }

  /**
   * @param false $all
   *
   * @return $this
   */
  public function clear(bool $all = false): EntityManager
  {
    $this->em->clear($all ? null : $this->class);
    return $this;
  }

  /**
   * @param $objects
   *
   * @return $this
   */
  public function deletes($objects): EntityManager
  {
    foreach($objects as $object)
    {
      $this->delete($object, false);
    }
    $this->em->flush();
    return $this;
  }

  /**
   * @return $this
   * @throws Exception
   */
  public function truncate(): EntityManager
  {
    $classMetaData = $this->getDoctrineEntityManager()->getClassMetadata($this->getClass());
    $connection = $this->getDoctrineEntityManager()->getConnection();
    $dbPlatform = $connection->getDatabasePlatform();
    $connection->beginTransaction();
    $isMysql = $connection->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\MySQL\Driver ? true : false;
    try {
      if($isMysql) {
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
      }
      $q = $dbPlatform->getTruncateTableSql(($classMetaData->getSchemaName() ? "{$classMetaData->getSchemaName()}." : "").$classMetaData->getTableName(), true);
      $connection->executeStatement($q);
      if($isMysql) {
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
      }
      $connection->commit();
    }
    catch (\Exception $e) {
      $connection->rollback();
      throw $e;
    }
    return $this;
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
    return $this->repository->retreiveById($id, $closure);
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
    return $this->repository->retreiveByKey($key, $value, $closure);
  }

  /**
   * @param \Closure|null $closure
   *
   * @return mixed
   * @throws NonUniqueResultException
   */
  public function retreiveByClosure(\Closure $closure = null)
  {
    return $this->repository->retreiveByClosure($closure);
  }

  /**
   * @param string $orderByAttribute
   * @param string $orderByType
   * @param \Closure|null $closure
   *
   * @return ArrayCollection|array
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = "ASC", \Closure $closure = null): array
  {
    return $this->getRepository()->selectAll($orderByAttribute, $orderByType, $closure);
  }

  /**
   * @return int
   * @param \Closure|null $closure
   * @throws NoResultException|NonUniqueResultException
   */
  public function countAll(\Closure $closure = null): int
  {
    return $this->getRepository()->countAll($closure);
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return ArrayCollection|array
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): array
  {
    return $this->getRepository()->selectByQueryBuilder($queryBuilder);
  }

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return ArrayCollection|array
   */
  public function selectByClosure(\Closure $closure, string $alias = "root"): array
  {
    return $this->getRepository()->selectByClosure($closure, $alias);
  }

  /**
   * @param \Closure $closure
   * @param string $alias
   *
   * @return bool
   */
  public function deleteByClosure(\Closure $closure, string $alias = "root"): bool
  {
    return $this->getRepository()->deleteByClosure($closure, $alias);
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return array
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): array
  {
    return $this->getRepository()->paginatorByQueryBuilder($queryBuilder);
  }

  /**
   * @param \Closure $closure
   * @param string $alias
   * @return array
   */
  public function paginatorByClosure(\Closure $closure, string $alias = "root"): array
  {
    return $this->getRepository()->paginatorByClosure($closure, $alias);
  }

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return int
   * @throws NoResultException|NonUniqueResultException
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int
  {
    return $this->getRepository()->countByQueryBuilder($queryBuilder);
  }

  /**
   * @return EntityRepositoryInterface|EntityRepository|ObjectRepository
   *@var string|null $entity
   */
  public function getRepository(string $entity = null)
  {
    $repository = $entity ? $this->getDoctrineEntityManager()->getRepository($entity) : $this->repository;
    if($repository instanceof EntityRepositoryInterface)
    {
      $repository->setDispatcher($this->dispatcher);
    }
    return $repository;
  }

}
