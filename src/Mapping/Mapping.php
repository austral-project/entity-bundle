<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Mapping;

use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\EntityAttributes\EntitiesAttributes;
use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;
use Austral\ToolsBundle\Services\Debug;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Austral EntityMapping.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class Mapping
{
  /**
   * @var array<string, EntityMapping<T>>
   */
  private array $entitiesMapping = [];

  /**
   * @var array<string, class-string>
   */
  private array $sluggers = [];

  /**
   * @var bool
   */
  private bool $initialised = false;

  /**
   * @var bool
   */
  private bool $initialising = false;

  /**
   * Mapping constructor
   *
   * @param EntityManager<T>         $entityManager
   * @param Debug                    $debug
   * @param EventDispatcherInterface $eventDispatcher
   * @param \Closure                 $entitiesAttributes
   */
  public function __construct(
    private EntityManager $entityManager,
    private Debug $debug,
    private EventDispatcherInterface $eventDispatcher,
    private \Closure $entitiesAttributes
  ) {
  }

  /**
   * @return Debug
   */
  protected function getDebug(): Debug
  {
    return $this->debug;
  }

  /**
   * initialise
   *
   * @return void
   *
   * @throws \Throwable
   */
  private function initialise(): void
  {
    if ($this->initialised || $this->initialising)
    {
      return;
    }
    $this->initialising = true;
    $this->debug->stopWatchStart('initialise', 'entity.mapping');
    try
    {
      $this->sluggers = [];
      /** @var EntitiesAttributes<T> $entitiesAttributes */
      $entitiesAttributes = ($this->entitiesAttributes)();
      $this->entitiesMapping = [];
      $this->eventDispatcher->dispatch(new EntityMappingEvent($this, $entitiesAttributes), EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING);
      $this->initialised = true;
    }
    finally
    {
      $this->initialising = false;
      $this->debug->stopWatchStop('initialise');
    }
  }

  /**
   * @return array<string, EntityMapping<T>>
   *
   * @throws \Throwable
   */
  public function getEntitiesMapping(): array
  {
    $this->initialise();

    return $this->entitiesMapping;
  }

  /**
   * @param class-string     $classname
   * @param EntityMapping<T> $entityMapping
   *
   * @return $this
   *
   * @throws \Throwable
   */
  public function addEntityMapping(string $classname, EntityMapping $entityMapping): Mapping
  {
    $this->initialise();
    $entityMapping->setMapping($this);
    $this->entitiesMapping[$classname] = $entityMapping;
    $this->sluggers[$entityMapping->slugger] = $classname;

    return $this;
  }

  /**
   * @param string $entityClass
   *
   * @return EntityMapping<T>|null
   *
   * @throws \Throwable
   */
  public function getEntityMapping(string $entityClass): ?EntityMapping
  {
    $this->initialise();
    if (array_key_exists($entityClass, $this->sluggers))
    {
      $entityClass = $this->sluggers[$entityClass];
    }
    if (array_key_exists($entityClass, $this->entitiesMapping))
    {
      return $this->entitiesMapping[$entityClass];
    }

    return null;
  }

  /**
   * @param string $entityClass
   *
   * @return array<string, array<string, FieldMappingInterface<T>>>|array<null>
   *
   * @throws \Throwable
   */
  public function getFieldsMapping(string $entityClass): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMapping() : [];
  }

  /**
   * @param string       $entityClass
   * @param class-string $fieldMappingClass
   * @param string       $fieldname
   *
   * @return FieldMappingInterface<T>|null
   *
   * @throws \Throwable
   */
  public function getFieldsMappingByFieldname(string $entityClass, string $fieldMappingClass, string $fieldname): ?FieldMappingInterface
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMappingByFieldname($fieldMappingClass, $fieldname) : null;
  }

  /**
   * @param string       $entityClass
   * @param class-string $fieldMappingClass
   *
   * @return array<string, FieldMappingInterface<T>>|array<null>
   *
   * @throws \Throwable
   */
  public function getFieldsMappingByClass(string $entityClass, string $fieldMappingClass): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMappingByClass($fieldMappingClass) : [];
  }

  /**
   * @param string $entityClass
   * @param string $entityClassMapping
   *
   * @return EntityClassMappingInterface<T>|null
   *
   * @throws \Throwable
   */
  public function getEntityClassMapping(string $entityClass, string $entityClassMapping): ?EntityClassMappingInterface
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getEntityClassMapping($entityClassMapping) : null;
  }

  /**
   * @param string $entityClass
   * @param string $fieldname
   *
   * @return array<int, FieldMappingInterface<T>>|array<null>
   *
   * @throws \Throwable
   */
  public function getAllFieldsMappingClassByFieldname(string $entityClass, string $fieldname): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getAllFieldsMappingClassByFieldname($fieldname) : [];
  }

  /**
   * @param string $classOrSlugger
   * @param string $id
   *
   * @return EntityInterface<object>|null any entity class known by the mapping, not only T
   *
   * @throws \Throwable
   */
  public function getObject(string $classOrSlugger, string $id): ?EntityInterface
  {
    if ($entitiesFileMapping = $this->getEntityMapping($classOrSlugger))
    {
      $repository = $this->entityManager->getDoctrineEntityManager()->getRepository($entitiesFileMapping->entityClass);
      if ($repository instanceof EntityRepositoryInterface)
      {
        return $repository->retrieveById($id);
      }
    }

    return null;
  }

  /**
   * @param string $entityClass
   *
   * @return string|null
   *
   * @throws \Throwable
   */
  public function getSlugger(string $entityClass): ?string
  {
    return $this->getEntityMapping($entityClass) ? $this->getEntityMapping($entityClass)->slugger : null;
  }
}
