<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityAttributes;

use Austral\EntityBundle\EntityManager\EntityManager;
use Austral\EntityBundle\Attribute\AustralEntityAttributeInterface;
use Austral\EntityBundle\Attribute\Slugger;

use Austral\ToolsBundle\Services\Debug;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral EntitiesAttributes.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntitiesAttributes
{

  /**
   * @var EntityManager<T>
   */
  protected EntityManager $entityManager;

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * @var array<string, EntityAttributes<T>>
   */
  protected array $entitiesAttributes = [];

  /**
   * ControllerListener constructor.
   *
   * @param EntityManager<T> $entityManager
   * @param Debug $debug
   *
   * @throws \ReflectionException
   */
  public function __construct(
    #[Autowire(service: 'austral.entity_manager')] EntityManager $entityManager,
    #[Autowire(service: 'austral.tools.debug')] Debug $debug,
  ) {
    $this->entityManager = $entityManager;
    $this->debug = $debug;
    $this->initMetadata();
  }

  /**
   * @return void
   * @throws \ReflectionException
   */
  protected function initMetadata(): void
  {
    $metadatas = $this->entityManager->getDoctrineEntityManager()->getMetadataFactory()->getAllMetadata();

    /** @var ClassMetadata<T> $classMetadata */
    foreach($metadatas as $classMetadata) {
      if(!$classMetadata->isMappedSuperclass) {

        /** @var EntityAttributes<T> $entityAttribute */
        $entityAttribute = EntityAttributes::create($classMetadata);

        $this->addAttributesByClass($classMetadata, $entityAttribute);
        $this->addAttributesByFields($classMetadata, $entityAttribute);
        $this->addAttributesByProperties($classMetadata, $entityAttribute);
        $this->addAttributesByMethods($classMetadata, $entityAttribute);

        $this->entitiesAttributes[$classMetadata->getName()] = $entityAttribute;
      }
    }
  }

  /**
   * @return EntityManager<T>
   */
  public function getEntityManager(): EntityManager
  {
    return $this->entityManager;
  }

  /**
   * @return array<string, EntityAttributes<T>>
   */
  public function all(): array
  {
    return $this->entitiesAttributes;
  }

  /**
   * @param string $classname
   *
   * @return EntityAttributes<T>|null
   */
  public function getEntityAttributes(string $classname): ?EntityAttributes
  {
    return array_key_exists($classname, $this->entitiesAttributes) ? $this->entitiesAttributes[$classname] : null;
  }

  /**
   * @param string $classname
   *
   * @return $this
   */
  public function removeEntityAttributes(string $classname): EntitiesAttributes
  {
    if(array_key_exists($classname, $this->entitiesAttributes))
    {
      unset($this->entitiesAttributes[$classname]);
    }
    return $this;
  }

  /**
   * @param ClassMetadata<T> $classMetadata
   * @param EntityAttributes<T> $entityAttribute
   *
   * @return void
   * @throws \ReflectionException
   */
  public function addAttributesByClass(ClassMetadata $classMetadata, EntityAttributes $entityAttribute): void
  {
    // getReflectionClass() already returns a \ReflectionClass: wrapping it again in
    // new \ReflectionClass() would reflect the ReflectionClass class itself.
    $reflection = $classMetadata->getReflectionClass();
    foreach($reflection->getAttributes() as $attribute)
    {
      if(is_a($attribute->getName(), Slugger::class, true))
      {
        /** @var Slugger $slugger */
        $slugger = $attribute->newInstance();
        if($slugger->slugger)
        {
          $entityAttribute->setSlugger($slugger->slugger);
        }
      }
    }
    $this->addClassAttributes($reflection, $entityAttribute);
    if($parentReflection = $reflection->getParentClass())
    {
      // Attributes declared on the entity itself take precedence over the parent ones.
      $this->addClassAttributes($parentReflection, $entityAttribute, false);
    }
  }

  /**
   * @param \ReflectionClass<object> $reflection
   * @param EntityAttributes<T> $entityAttribute
   * @param bool $override
   *
   * @return void
   */
  protected function addClassAttributes(\ReflectionClass $reflection, EntityAttributes $entityAttribute, bool $override = true): void
  {
    foreach($reflection->getAttributes() as $attribute)
    {
      if(!is_a($attribute->getName(), AustralEntityAttributeInterface::class, true))
      {
        continue;
      }
      if(!$override && array_key_exists($attribute->getName(), $entityAttribute->getClassAttributes()))
      {
        continue;
      }
      /** @var AustralEntityAttributeInterface $attributeInstance */
      $attributeInstance = $attribute->newInstance();
      $attributeInstance->setKeyname($entityAttribute->getClassname());
      $entityAttribute->addClassAttribute($attributeInstance);
    }
  }

  /**
   * @param ClassMetadata<T> $classMetadata
   * @param EntityAttributes<T> $entityAttribute
   *
   * @return void
   * @throws \ReflectionException
   */
  public function addAttributesByFields(ClassMetadata $classMetadata, EntityAttributes $entityAttribute): void
  {
    $reflection = $classMetadata->getReflectionClass();
    foreach($classMetadata->fieldMappings as $fieldname => $field)
    {
      // Embedded fields ("embedded.field") have no matching property on the entity.
      if(!$reflection->hasProperty($fieldname))
      {
        continue;
      }
      $this->addFieldAttributes($reflection->getProperty($fieldname)->getAttributes(), $fieldname, $entityAttribute);
    }
  }

  /**
   * @param ClassMetadata<T> $classMetadata
   * @param EntityAttributes<T> $entityAttribute
   *
   * @return void
   * @throws \ReflectionException
   */
  public function addAttributesByProperties(ClassMetadata $classMetadata, EntityAttributes $entityAttribute): void
  {
    foreach($classMetadata->getReflectionClass()->getProperties() as $property)
    {
      if(!array_key_exists($property->getName(), $classMetadata->fieldMappings))
      {
        $this->addFieldAttributes($property->getAttributes(), $property->getName(), $entityAttribute);
      }
    }
  }

  /**
   * @param ClassMetadata<T> $classMetadata
   * @param EntityAttributes<T> $entityAttribute
   *
   * @return void
   * @throws \ReflectionException
   */
  public function addAttributesByMethods(ClassMetadata $classMetadata, EntityAttributes $entityAttribute): void
  {
    foreach($classMetadata->getReflectionClass()->getMethods() as $method)
    {
      $this->addFieldAttributes($method->getAttributes(), $method->getName(), $entityAttribute);
    }
  }

  /**
   * Register the Austral attributes of a field, property or method, keyed by its name.
   *
   * @param array<\ReflectionAttribute<object>> $attributes
   * @param string $keyname
   * @param EntityAttributes<T> $entityAttribute
   *
   * @return void
   */
  protected function addFieldAttributes(array $attributes, string $keyname, EntityAttributes $entityAttribute): void
  {
    foreach($attributes as $attribute)
    {
      if(!is_a($attribute->getName(), AustralEntityAttributeInterface::class, true))
      {
        continue;
      }
      /** @var AustralEntityAttributeInterface $attributeInstance */
      $attributeInstance = $attribute->newInstance();
      $attributeInstance->setKeyname($keyname);
      $entityAttribute->addFieldAttribute($attributeInstance);
    }
  }

  /**
   * @param object|string $objectOrClass
   *
   * @return EntityAttributes<T>|null
   */
  public function getAttributesByEntity(object|string $objectOrClass): ?EntityAttributes
  {
    $currentClass = $this->getClassname($objectOrClass);
    if(array_key_exists($currentClass, $this->entitiesAttributes))
    {
      return $this->entitiesAttributes[$currentClass];
    }
    return null;
  }

  /**
   * @param object|string $objectOrClass
   *
   * @return string
   */
  protected function getClassname(object|string $objectOrClass): string
  {
    return is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;
  }

}
