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
use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Austral\EntityBundle\Paginator\PaginatorResult;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\ClassMetadata;
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
   * @return array<array-key, EntityInterface<T>>
   *
   * @throws QueryException
   */
  public function selectAll(string $orderByAttribute = 'id', string $orderByType = 'ASC', ?callable $closure = null): array;

  /**
   * @param QueryBuilder $queryBuilder
   *
   * @return array<array-key, EntityInterface<T>>
   */
  public function selectByQueryBuilder(QueryBuilder $queryBuilder): array;

  /**
   * @param (callable(QueryBuilder): void)|null $closure
   * @param non-empty-string                    $alias
   *
   * @return array<array-key, EntityInterface<T>>
   */
  public function selectByClosure(?callable $closure, string $alias = 'root'): array;

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

  /**
   * @param array<string, mixed> $values
   *
   * @return EntityInterface<T>
   */
  public function create(array $values = []): EntityInterface;

  /**
   * @return $this
   *
   * @throws \Exception
   */
  public function truncate(): self;

  /**
   * @param EntityInterface<T> $object
   * @param bool               $andFlush
   *
   * @return $this
   */
  public function delete(EntityInterface $object, bool $andFlush = true): self;

  /**
   * @param array<int|string, EntityInterface<T>> $objects
   *
   * @return $this
   */
  public function deletes(array $objects): self;

  /**
   * @param class-string<T>|null $entity
   *
   * @return EntityRepositoryInterface<object>|EntityRepository<object>|null
   */
  public function getRepository(?string $entity = null): EntityRepositoryInterface|EntityRepository|null;

  /**
   * @return $this
   */
  public function flush(): self;

  /**
   * @param EntityInterface<T> $object
   *
   * @return $this
   *
   * @throws ORMException
   */
  public function refresh(EntityInterface $object): self;

  /**
   * @param EntityInterface<T> $sourceObject
   *
   * @return EntityInterface<T>
   */
  public function duplicate(EntityInterface $sourceObject): EntityInterface;

  /**
   * @param class-string|null $class
   *
   * @return ClassMetadata<object>|null
   */
  public function getClassMetadata(?string $class = null): ?ClassMetadata;

  /**
   * @param class-string|null $class
   *
   * @return array<string, mixed>
   */
  public function getFieldsMapping(?string $class = null): array;

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMappingWithAssociation(): array;

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMappingAll(): array;

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   *
   * @return $this
   */
  public function addEntityManagerEvent(EntityManagerEvent $entityManagerEvent): self;

  /**
   * @param string|null $currentLanguage
   *
   * @return $this
   */
  public function setCurrentLanguage(?string $currentLanguage = null): self;

  /**
   * @return bool
   */
  public function isPgsql(): bool;

  /**
   * getDriver
   *
   * @return \Doctrine\DBAL\Driver
   */
  public function getDriver(): \Doctrine\DBAL\Driver;

  /**
   * @param object $event
   * @param string $eventName
   */
  public function dispatch(object $event, string $eventName): void;

  /**
   * @param class-string<T> $class
   *
   * @return $this
   */
  public function setClass(string $class): self;

  /**
   * @return $this
   */
  public function clear(): self;
}
