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
use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Event\EntityManagerMappingEvent;
use Austral\EntityBundle\Model\PaginatorResult;
use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Abstract EntityManager.
 *
 * @template T of object
 *
 * @implements EntityManagerInterface<T>
 * @implements EntityManagerORMInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityManager implements EntityManagerInterface, EntityManagerORMInterface
{
  /**
   * @var DoctrineEntityManagerInterface
   */
  protected DoctrineEntityManagerInterface $em;

  /**
   * @var EntityRepositoryInterface<T>|null
   */
  protected ?EntityRepositoryInterface $repository = null;

  /**
   * @var class-string<T>|null
   */
  protected ?string $class = null;

  /**
   * @var string|null
   */
  protected ?string $currentLanguage = null;

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $dispatcher;

  /**
   * @var array<int|string, EntityManagerEvent<T>>
   */
  protected array $entityManagerEvents = [];

  /**
   * Constructor.
   *
   * @param DoctrineEntityManagerInterface $em
   * @param EventDispatcherInterface       $dispatcher
   * @param class-string<T>|null           $class
   */
  public function __construct(
    #[Autowire(service: 'doctrine.orm.entity_manager')]
    DoctrineEntityManagerInterface $em,
    #[Autowire(service: 'event_dispatcher')]
    EventDispatcherInterface $dispatcher,
    ?string $class = null
  ) {
    $this->em = $em;
    $this->dispatcher = $dispatcher;
    $this->initRepository($class);
  }

  /**
   * initRepository
   *
   * @param class-string<T>|null $class
   *
   * @return $this
   */
  protected function initRepository(?string $class): self
  {
    if ($class)
    {
      $repository = $this->em->getRepository($class);
      if ($repository instanceof EntityRepositoryInterface)
      {
        $this->repository = $repository;
        $this->repository->setDispatcher($this->dispatcher);
      }
      $this->class = $this->em->getClassMetadata($class)->getName();
    }

    return $this;
  }

  /**
   * @return class-string<T>|null $class
   */
  public function getClass(): ?string
  {
    return $this->class;
  }

  /**
   * @param class-string<T> $class
   *
   * @return $this
   */
  public function setClass(string $class): EntityManager
  {
    $this->initRepository($class);

    return $this;
  }

  /**
   * @param object $event
   * @param string $eventName
   */
  public function dispatch(object $event, string $eventName): void
  {
    $this->dispatcher->dispatch($event, $eventName);
  }

  /**
   * getDriver
   *
   * @return \Doctrine\DBAL\Driver
   */
  public function getDriver(): \Doctrine\DBAL\Driver
  {
    return $this->getDoctrineEntityManager()->getConnection()->getDriver();
  }

  /**
   * @return bool
   */
  public function isPgsql(): bool
  {
    return $this->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
  }

  /**
   * @param string|null $currentLanguage
   *
   * @return $this
   */
  public function setCurrentLanguage(?string $currentLanguage = null): EntityManager
  {
    $this->currentLanguage = $currentLanguage;

    return $this;
  }

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   *
   * @return $this
   */
  public function addEntityManagerEvent(EntityManagerEvent $entityManagerEvent): EntityManager
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
   * @return array<string, mixed>
   */
  public function getFieldsMappingAll(): array
  {
    return array_merge($this->getFieldsMappingWithAssociation(), $this->getFieldsMapping());
  }

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMappingWithAssociation(): array
  {
    $associationMappings = [];
    if ($classMetadata = $this->getClassMetadata($this->getClass()))
    {
      $associationMappings = $classMetadata->associationMappings;
    }
    $mappingEvent = new EntityManagerMappingEvent($this, $associationMappings);
    $this->dispatch($mappingEvent, EntityManagerMappingEvent::EVENT_NAME);

    return $mappingEvent->getFieldsMapping();
  }

  /**
   * @param class-string|null $class
   *
   * @return array<string, mixed>
   */
  public function getFieldsMapping(?string $class = null): array
  {
    if ($classMetadata = $this->getClassMetadata($class))
    {
      return $classMetadata->fieldMappings;
    }

    return [];
  }

  /**
   * @param class-string|null $class
   *
   * @return ClassMetadata<object>|null
   */
  public function getClassMetadata(?string $class = null): ?ClassMetadata
  {
    return $class ? $this->em->getClassMetadata($class) : ($this->class ? $this->em->getClassMetadata($this->class) : null);
  }

  /**
   * @param EntityInterface<T> $sourceObject
   *
   * @return EntityInterface<T>
   */
  public function duplicate(EntityInterface $sourceObject): EntityInterface
  {
    $objectDuplicate = clone $sourceObject;
    $entityManagerEvent = new EntityManagerEvent('duplicate', $this, $objectDuplicate);
    $entityManagerEvent->setSourceObject($sourceObject);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_DUPLICATE);
    $this->addEntityManagerEvent($entityManagerEvent);

    return $objectDuplicate;
  }

  /**
   * @param array<string, mixed> $values
   *
   * @return EntityInterface<T>
   */
  public function create(array $values = []): EntityInterface
  {
    $class = $this->getClass();
    $object = new $class();
    $entityManagerEvent = new EntityManagerEvent('create', $this, $object);
    $entityManagerEvent->setHydrateValues($values);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_CREATE);
    $this->addEntityManagerEvent($entityManagerEvent);

    return $object;
  }

  /**
   * @param EntityInterface<T> $object
   * @param bool               $andFlush
   * @param bool               $dispatchToFlush
   *
   * @return $this
   */
  public function update(EntityInterface $object, bool $andFlush = true, bool $dispatchToFlush = false): EntityManager
  {
    $entityManagerEvent = new EntityManagerEvent('update', $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_UPDATE);
    $this->addEntityManagerEvent($entityManagerEvent);
    $entityManagerEvent->setDispatchToFlush($dispatchToFlush);
    $this->em->persist($object);
    if ($andFlush)
    {
      $this->flush();
    }

    return $this;
  }

  /**
   * @param EntityInterface<T> $object
   * @param bool               $andFlush
   *
   * @return $this
   */
  public function delete(EntityInterface $object, bool $andFlush = true): EntityManager
  {
    $entityManagerEvent = new EntityManagerEvent('delete', $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_DELETE);
    $this->addEntityManagerEvent($entityManagerEvent);
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
    if ($this->entityManagerEvents)
    {
      /** @var EntityManagerEvent<T> $entityManagerEvent */
      foreach ($this->entityManagerEvents as $entityManagerEvent)
      {
        if ($entityManagerEvent->getDispatchToFlush())
        {
          $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_BEFORE);
        }
      }
    }
    $this->em->flush();
    if ($this->entityManagerEvents)
    {
      /** @var EntityManagerEvent<T> $entityManagerEvent */
      foreach ($this->entityManagerEvents as $entityManagerEvent)
      {
        if ($entityManagerEvent->getDispatchToFlush())
        {
          $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_AFTER);
        }
      }
    }

    return $this;
  }

  /**
   * @param EntityInterface<T> $object
   *
   * @return $this
   *
   * @throws ORMException
   */
  public function refresh(EntityInterface $object): self
  {
    $this->em->refresh($object);

    return $this;
  }

  /**
   * @return $this
   */
  public function clear(): self
  {
    $this->em->clear();

    return $this;
  }

  /**
   * @param array<int|string, EntityInterface<T>> $objects
   *
   * @return $this
   */
  public function deletes(array $objects): self
  {
    /** @var EntityInterface<T> $object */
    foreach ($objects as $object)
    {
      $this->delete($object, false);
    }
    $this->em->flush();

    return $this;
  }

  /**
   * @return $this
   *
   * @throws Exception
   * @throws \Exception
   */
  public function truncate(): EntityManager
  {
    if ($class = $this->getClass())
    {
      $classMetaData = $this->getDoctrineEntityManager()->getClassMetadata($class);
      $connection = $this->getDoctrineEntityManager()->getConnection();
      $dbPlatform = $connection->getDatabasePlatform();
      $connection->beginTransaction();
      $isMysql = $connection->getDriver() instanceof Driver;
      try
      {
        if ($isMysql)
        {
          $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        }
        $q = $dbPlatform->getTruncateTableSql(($classMetaData->getSchemaName() ? "{$classMetaData->getSchemaName()}." : '').$classMetaData->getTableName(), true);
        $connection->executeStatement($q);
        if ($isMysql)
        {
          $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }
        $connection->commit();
      }
      catch (\Exception $e)
      {
        $connection->rollback();
        throw $e;
      }
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function createQueryBuilder(string $alias = 'root', ?string $indexBy = null): ?QueryBuilder
  {
    return $this->repository?->createQueryBuilder($alias, $indexBy);
  }

  /**
   * @param string                              $id
   * @param (callable(QueryBuilder): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveById(string $id, ?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveById($id, $closure);
  }

  /**
   * @param string                                        $key
   * @param string                                        $value
   * @param (callable(AustralQueryBuilder<T>): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveByKey(string $key, string $value, ?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveByKey($key, $value, $closure);
  }

  /**
   * @param (callable(AustralQueryBuilder<T>): void)|null $closure
   *
   * @return EntityInterface<T>|null
   *
   * @throws NonUniqueResultException
   */
  public function retrieveByClosure(?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveByClosure($closure);
  }

  /**
   * @param non-empty-string                              $orderByAttribute
   * @param 'ASC'|'DESC'                                  $orderByType
   * @param (callable(AustralQueryBuilder<T>): void)|null $closure
   *
   * @return Collection<int, EntityInterface<T>>|null
   *
   * @throws QueryException
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = 'ASC', ?callable $closure = null): ?Collection
  {
    return $this->repository?->selectAll($orderByAttribute, $orderByType, $closure);
  }

  /**
   * {@inheritDoc}
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): ?Collection
  {
    return $this->repository?->selectByQueryBuilder($queryBuilder);
  }

  /**
   * {@inheritDoc}
   */
  public function selectByClosure(?callable $closure, string $alias = 'root'): ?Collection
  {
    return $this->repository?->selectByClosure($closure, $alias);
  }

  /**
   * {@inheritDoc}
   */
  public function selectArrayLanguages(EntityInterface $object, ?callable $closure = null): array
  {
    $selectLanguages = $this->repository?->selectArrayLanguages($object, $closure);

    return is_iterable($selectLanguages) ? iterator_to_array($selectLanguages) : [];
  }

  /**
   * {@inheritDoc}
   */
  public function deleteByClosure(?callable $closure, string $alias = 'root'): bool
  {
    $value = $this->repository?->deleteByClosure($closure, $alias);

    return $value ?? false;
  }

  /**
   * {@inheritDoc}
   */
  public function paginatorByQueryBuilder(QueryBuilder $queryBuilder): PaginatorResult
  {
    if (!$paginator = $this->repository?->paginatorByQueryBuilder($queryBuilder))
    {
      /** @var PaginatorResult<T> $paginator */
      $paginator = new PaginatorResult(null, new \Exception('Repository is not defined !'));
    }

    return $paginator;
  }

  /**
   * {@inheritDoc}
   */
  public function paginatorByClosure(?callable $closure, string $alias = 'root'): PaginatorResult
  {
    if (!$paginator = $this->repository?->paginatorByClosure($closure, $alias))
    {
      /** @var PaginatorResult<T> $paginator */
      $paginator = new PaginatorResult(null, new \Exception('Repository is not defined !'));
    }

    return $paginator;
  }

  /**
   * {@inheritDoc}
   */
  public function countAll(?callable $closure = null): int
  {
    $value = $this->repository?->countAll($closure);

    return is_integer($value) ? $value : 0;
  }

  /**
   * {@inheritDoc}
   */
  public function countByQueryBuilder(QueryBuilder $queryBuilder): int
  {
    $value = $this->repository?->countByQueryBuilder($queryBuilder);

    return is_integer($value) ? $value : 0;
  }

  /**
   * {@inheritDoc}
   */
  public function queryBuilderExtends(string $name, AustralQueryBuilder $queryBuilder): AustralQueryBuilder
  {
    $queryBuilderReturn = $this->repository?->queryBuilderExtends($name, $queryBuilder);

    return $queryBuilderReturn ?: $queryBuilder;
  }

  /**
   * @param class-string<T>|null $entity
   *
   * @return EntityRepositoryInterface<object>|EntityRepository<object>|null
   */
  public function getRepository(?string $entity = null): EntityRepositoryInterface|EntityRepository|null
  {
    $repository = $entity ? $this->getDoctrineEntityManager()->getRepository($entity) : $this->repository;
    if ($repository instanceof EntityRepositoryInterface)
    {
      $this->repository = $repository;
      $repository->setDispatcher($this->dispatcher);
    }

    return $repository;
  }
}
