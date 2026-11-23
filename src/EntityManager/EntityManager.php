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
use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Austral\EntityBundle\Paginator\PaginatorResult;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
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
   * @var array<int|string, EntityManagerEvent<T>>
   */
  protected array $entityManagerEvents = [];

  /**
   * Constructor.
   *
   * @param DoctrineEntityManagerInterface $doctrineEntityManager
   * @param EventDispatcherInterface       $eventDispatcher
   * @param class-string<T>|null           $class
   */
  public function __construct(
    protected DoctrineEntityManagerInterface $doctrineEntityManager,
    protected EventDispatcherInterface $eventDispatcher,
    ?string $class = null
  ) {
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
      $repository = $this->doctrineEntityManager->getRepository($class);
      if ($repository instanceof EntityRepositoryInterface)
      {
        $this->repository = $repository;
        $this->repository->setDispatcher($this->eventDispatcher);
      }
      $this->class = $this->doctrineEntityManager->getClassMetadata($class)->getName();
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
   * {@inheritDoc}
   */
  public function dispatch(object $eventDispatcher, string $eventName): void
  {
    $this->eventDispatcher->dispatch($eventDispatcher, $eventName);
  }

  /**
   * {@inheritDoc}
   */
  public function getDriver(): \Doctrine\DBAL\Driver
  {
    return $this->getDoctrineEntityManager()->getConnection()->getDriver();
  }

  /**
   * {@inheritDoc}
   */
  public function isPgsql(): bool
  {
    return $this->getDriver() instanceof \Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
  }

  /**
   * {@inheritDoc}
   */
  public function setCurrentLanguage(?string $currentLanguage = null): self
  {
    $this->currentLanguage = $currentLanguage;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function addEntityManagerEvent(EntityManagerEvent $entityManagerEvent): self
  {
    $this->entityManagerEvents[] = $entityManagerEvent;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getDoctrineEntityManager(): DoctrineEntityManagerInterface
  {
    return $this->doctrineEntityManager;
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldsMappingAll(): array
  {
    return array_merge($this->getFieldsMappingWithAssociation(), $this->getFieldsMapping());
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  public function getClassMetadata(?string $class = null): ?ClassMetadata
  {
    return $class ? $this->doctrineEntityManager->getClassMetadata($class) : ($this->class ? $this->doctrineEntityManager->getClassMetadata($this->class) : null);
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  public function update(EntityInterface $object, bool $andFlush = true, bool $dispatchToFlush = false): self
  {
    $entityManagerEvent = new EntityManagerEvent('update', $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_UPDATE);
    $this->addEntityManagerEvent($entityManagerEvent);
    $entityManagerEvent->setDispatchToFlush($dispatchToFlush);
    $this->doctrineEntityManager->persist($object);
    if ($andFlush)
    {
      $this->flush();
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function delete(EntityInterface $object, bool $andFlush = true): self
  {
    $entityManagerEvent = new EntityManagerEvent('delete', $this, $object);
    $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_DELETE);
    $this->addEntityManagerEvent($entityManagerEvent);
    $entityManagerEvent->setDispatchToFlush(true);
    $this->doctrineEntityManager->remove($object);
    if ($andFlush)
    {
      $this->flush();
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function flush(): self
  {
    $entityManagerEvents = array_filter($this->entityManagerEvents, static fn (EntityManagerEvent $entityManagerEvent): bool => $entityManagerEvent->getDispatchToFlush());
    $this->entityManagerEvents = [];

    /** @var EntityManagerEvent<T> $entityManagerEvent */
    foreach ($entityManagerEvents as $entityManagerEvent)
    {
      $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_BEFORE);
    }
    $this->doctrineEntityManager->flush();
    /** @var EntityManagerEvent<T> $entityManagerEvent */
    foreach ($entityManagerEvents as $entityManagerEvent)
    {
      $this->dispatch($entityManagerEvent, EntityManagerEvent::EVENT_PUSH_AFTER);
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function refresh(EntityInterface $object): self
  {
    $this->doctrineEntityManager->refresh($object);

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function clear(): self
  {
    $this->doctrineEntityManager->clear();

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function deletes(array $objects): self
  {
    /** @var EntityInterface<T> $object */
    foreach ($objects as $object)
    {
      $this->delete($object, false);
    }
    $this->flush();

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function truncate(): self
  {
    if ($class = $this->getClass())
    {
      $classMetaData = $this->getDoctrineEntityManager()->getClassMetadata($class);
      $connection = $this->getDoctrineEntityManager()->getConnection();
      $dbPlatform = $connection->getDatabasePlatform();
      $tableName = ($classMetaData->getSchemaName() ? "{$classMetaData->getSchemaName()}." : '').$classMetaData->getTableName();

      if ($dbPlatform instanceof AbstractMySQLPlatform)
      {
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        try
        {
          $connection->executeStatement($dbPlatform->getTruncateTableSQL($tableName));
        }
        finally
        {
          $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
        }
      }
      else
      {
        $connection->transactional(static fn (Connection $connection): int|string => $connection->executeStatement($dbPlatform->getTruncateTableSQL($tableName, true)));
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
   * {@inheritDoc}
   */
  public function retrieveById(string $id, ?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveById($id, $closure);
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveByKey(string $key, string $value, ?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveByKey($key, $value, $closure);
  }

  /**
   * {@inheritDoc}
   */
  public function retrieveByClosure(?callable $closure = null): ?EntityInterface
  {
    return $this->repository?->retrieveByClosure($closure);
  }

  /**
   * {@inheritDoc}
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = 'ASC', ?callable $closure = null): array
  {
    return $this->repository?->selectAll($orderByAttribute, $orderByType, $closure) ?? [];
  }

  /**
   * {@inheritDoc}
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): array
  {
    return $this->repository?->selectByQueryBuilder($queryBuilder) ?? [];
  }

  /**
   * {@inheritDoc}
   */
  public function selectByClosure(?callable $closure, string $alias = 'root'): array
  {
    return $this->repository?->selectByClosure($closure, $alias) ?? [];
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
      $paginator = new PaginatorResult([], 0, new \Exception('Repository is not defined !'));
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
      $paginator = new PaginatorResult([], 0, new \Exception('Repository is not defined !'));
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
    if (null === $entity)
    {
      return $this->repository;
    }

    $repository = $this->getDoctrineEntityManager()->getRepository($entity);
    if ($repository instanceof EntityRepositoryInterface)
    {
      $repository->setDispatcher($this->eventDispatcher);
    }

    return $repository;
  }
}
