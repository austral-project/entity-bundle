<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityAnnotation;

use Austral\EntityBundle\Annotation\AustralEntityAnnotationInterface;

use Austral\ToolsBundle\AustralTools;
use Austral\ToolsBundle\Services\Debug;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Austral EntitiesAnnotations.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntitiesAnnotations
{

  /**
   * @var EntityManager
   */
  protected EntityManager $entityManager;

  /**
   * @var Reader
   */
  protected Reader $reader;

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * @var array
   */
  protected array $entitiesAnnotations = array();

  /**
   * ControllerListener constructor.
   *
   * @param EntityManager $entityManager
   * @param Reader $reader
   * @param Debug $debug
   *
   * @throws \ReflectionException
   */
  public function __construct(EntityManager $entityManager, Reader $reader, Debug $debug)
  {
    $this->entityManager = $entityManager;
    $this->debug = $debug;
    $this->reader = $reader;
    $this->initMetadata();
  }

  /**
   * @return void
   * @throws \ReflectionException
   */
  protected function initMetadata()
  {
    $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
    /** @var ClassMetadata $classMetadata */
    foreach($metadatas as $classMetadata) {
      if(!$classMetadata->isMappedSuperclass) {
        $entityAnnotation = EntityAnnotations::create($classMetadata);
        $this->addAnnotationsByClass($classMetadata, $entityAnnotation);
        $this->addAnnotationsByFields($classMetadata, $entityAnnotation);
        $this->entitiesAnnotations[$classMetadata->getName()] = $entityAnnotation;
      }
    }
  }

  /**
   * @return EntityManager
   */
  public function getEntityManager(): EntityManager
  {
    return $this->entityManager;
  }

  /**
   * @return array
   */
  public function all(): array
  {
    return $this->entitiesAnnotations;
  }

  /**
   * @param string $classname
   *
   * @return EntityAnnotations|null
   */
  public function getEntityAnnotations(string $classname): ?EntityAnnotations
  {
    return array_key_exists($classname, $this->entitiesAnnotations) ? $this->entitiesAnnotations[$classname] : null;
  }

  /**
   * @param string $classname
   *
   * @return EntitiesAnnotations
   */
  public function removeEntityAnnotations(string $classname): EntitiesAnnotations
  {
    if(array_key_exists($classname, $this->entitiesAnnotations))
    {
      unset($this->entitiesAnnotations[$classname]);
    }
    return $this;
  }

  /**
   * @param ClassMetadata $classMetadata
   * @param EntityAnnotations $entityAnnotation
   *
   * @return void
   */
  public function addAnnotationsByClass(ClassMetadata $classMetadata, EntityAnnotations $entityAnnotation)
  {
    foreach($this->reader->getClassAnnotations($classMetadata->getReflectionClass()) as $annotation)
    {
      if(AustralTools::usedImplements($annotation, AustralEntityAnnotationInterface::class))
      {
        $annotation->setKeyname($entityAnnotation->getClassname());
        $entityAnnotation->addClassAnnotation($annotation);
      }
    }
    if($parentClass = $classMetadata->getReflectionClass()->getParentClass())
    {
      foreach($this->reader->getClassAnnotations($parentClass) as $annotation)
      {
        if(AustralTools::usedImplements($annotation, AustralEntityAnnotationInterface::class))
        {
          $annotation->setKeyname($entityAnnotation->getClassname());
          $entityAnnotation->addClassAnnotation($annotation);
        }
      }
    }

  }

  /**
   * @param ClassMetadata $classMetadata
   * @param EntityAnnotations $entityAnnotation
   *
   * @return void
   * @throws \ReflectionException
   */
  public function addAnnotationsByFields(ClassMetadata $classMetadata, EntityAnnotations $entityAnnotation)
  {
    foreach($classMetadata->fieldMappings as $fieldname => $field)
    {
      $properties = $classMetadata->getReflectionClass()->getProperty($fieldname);
      foreach($this->reader->getPropertyAnnotations($properties) as $annotation)
      {
        if(AustralTools::usedImplements($annotation, AustralEntityAnnotationInterface::class))
        {
          $annotation->setKeyname($fieldname);
          $entityAnnotation->addFieldAnnotation($annotation);
        }
      }
    }
  }

  /**
   * @param $objectOrClass
   *
   * @return EntityAnnotations|null
   */
  public function getAnnotationsByEntity($objectOrClass): ?EntityAnnotations
  {
    $currentClass = $this->getClassname($objectOrClass);
    if(array_key_exists($currentClass, $this->entitiesAnnotations))
    {
      return $this->entitiesAnnotations[$currentClass];
    }
    return null;
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
