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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Repository\RepositoryInterface;
use Austral\ToolsBundle\Services\Debug;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Austral EntityMapping.
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
   * @var EntityManager
   */
  protected EntityManager $entityManager;

  /**
   * @var array
   */
  protected array $entitiesMapping = array();

  /**
   * @var array
   */
  protected array $sluggers = array();

  /**
   * Constructor.
   */
  public function __construct(EntityManager $entityManager, Debug $debug)
  {
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
   * @return array
   */
  public function getEntitiesMapping(): array
  {
    return $this->entitiesMapping;
  }

  /**
   * @param string $classname
   * @param EntityMapping $entityMapping
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
   * @return EntityMapping|null
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
   * @return array
   */
  public function getFieldsMapping(string $entityClass): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMapping() : array();
  }

  /**
   * @param string $entityClass
   * @param string $fieldMappingClass
   * @param string $fieldname
   *
   * @return FieldMappingInterface
   */
  public function getFieldsMappingByFieldname(string $entityClass, string $fieldMappingClass, string $fieldname): ?FieldMappingInterface
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMappingByFieldname($fieldMappingClass, $fieldname) : null;
  }

  /**
   * @param string $entityClass
   * @param string $fieldMappingClass
   *
   * @return array
   */
  public function getFieldsMappingByClass(string $entityClass, string $fieldMappingClass): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getFieldsMappingByClass($fieldMappingClass) : array();
  }

  /**
   * @param string $entityClass
   * @param string $fieldname
   *
   * @return array
   */
  public function getAllFieldsMappingClassByFieldname(string $entityClass, string $fieldname): array
  {
    return ($this->getEntityMapping($entityClass)) ? $this->getEntityMapping($entityClass)->getAllFieldsMappingClassByFieldname($fieldname) : array();
  }

  /**
   * @param string $classOrSlugger
   * @param $id
   *
   * @return EntityInterface|null
   * @throws NonUniqueResultException
   */
  public function getObject(string $classOrSlugger, $id): ?EntityInterface
  {
    if($entitiesFileMapping = $this->getEntityMapping($classOrSlugger))
    {
      /** @var RepositoryInterface $repository */
      $repository = $this->entityManager->getDoctrineEntityManager()->getRepository($entitiesFileMapping->entityClass);
      return $repository->retreiveById($id);
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
   * @param $objectOrClass
   *
   * @return string
   */
  protected function getClassname($objectOrClass): string
  {
    return is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
  }

}
