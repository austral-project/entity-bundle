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
use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Repository\EntityRepositoryInterface;
use Austral\ToolsBundle\Services\Debug;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral EntityMapping.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
final Class Mapping
{

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * @var EntityManager<T>
   */
  protected EntityManager $entityManager;

  /**
   * @var array<string, EntityMapping<T>>
   */
  protected array $entitiesMapping = [];

  /**
   * @var array<string, class-string>
   */
  protected array $sluggers = [];

  /**
   * Mapping constructor
   *
   * @param EntityManager<T> $entityManager
   * @param Debug $debug
   */
  public function __construct(
    #[Autowire(service: 'austral.entity_manager')] EntityManager $entityManager,
    #[Autowire(service: 'austral.tools.debug')] Debug $debug,
  ) {
    $this->entityManager = $entityManager;
    $this->debug = $debug;
  }

  /**
   * @return Debug
   */
  public function getDebug(): Debug
  {
    return $this->debug;
  }

  /**
   * @return array<string, EntityMapping<T>>
   */
  public function getEntitiesMapping(): array
  {
    return $this->entitiesMapping;
  }

  /**
   * @param class-string $classname
   * @param EntityMapping<T> $entityMapping
   *
   * @return $this
   */
  public function addEntityMapping(string $classname, EntityMapping $entityMapping): Mapping
  {
    $entityMapping->setMapping($this);
    $this->entitiesMapping[$classname] = $entityMapping;
    $this->sluggers[$entityMapping->slugger] = $classname;
    return $this;
  }

  /**
   * @param string $entityClass
   *
   * @return EntityMapping<T>|null
   */
  public function getEntityMapping(string $entityClass): ?EntityMapping
  {
    if(array_key_exists($entityClass, $this->sluggers))
    {
      $entityClass = $this->sluggers[$entityClass];
    }
    if(array_key_exists($entityClass, $this->entitiesMapping))
    {
      return $this->entitiesMapping[$entityClass];
    }
    return null;
  }

  /**
   * @param string $entityClass
   *
   * @return array<string, array<string, FieldMappingInterface<T>>>|array<null>
   */
  public function getFieldsMapping(string $entityClass): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMapping() : [];
  }

  /**
   * @param string $entityClass
   * @param class-string $fieldMappingClass
   * @param string $fieldname
   *
   * @return FieldMappingInterface<T>|null
   */
  public function getFieldsMappingByFieldname(string $entityClass, string $fieldMappingClass, string $fieldname): ?FieldMappingInterface
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMappingByFieldname($fieldMappingClass, $fieldname) : null;
  }

  /**
   * @param string $entityClass
   * @param class-string $fieldMappingClass
   *
   * @return array<string, FieldMappingInterface<T>>|array<null>
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
   */
  public function getAllFieldsMappingClassByFieldname(string $entityClass, string $fieldname): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getAllFieldsMappingClassByFieldname($fieldname) : [];
  }

  /**
   * @param string $classOrSlugger
   * @param string $id
   *
   * @return EntityInterface<T>|null
   * @throws NonUniqueResultException
   */
  public function getObject(string $classOrSlugger, string $id): ?EntityInterface
  {
    if($entitiesFileMapping = $this->getEntityMapping($classOrSlugger))
    {
      /** @var EntityRepositoryInterface<T> $repository */
      $repository = $this->entityManager->getDoctrineEntityManager()->getRepository($entitiesFileMapping->entityClass);
      return $repository->retrieveById($id);
    }
    return null;
  }

  /**
   * @param string $entityClass
   *
   * @return string|null
   */
  public function getSlugger(string $entityClass): ?string
  {
    return $this->getEntityMapping($entityClass) ? $this->getEntityMapping($entityClass)->slugger : null;
  }

  /**
   * @param object|class-string $objectOrClass
   *
   * @return class-string
   */
  protected function getClassname(object|string $objectOrClass): string
  {
    return is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
  }

}
