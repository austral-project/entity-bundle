<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Services;

use Austral\EntityBundle\Attribute\AustralEntitySanitize;
use Austral\EntityBundle\Sanitizer\AustralSanitizerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/**
 * Applies the sanitizer configurations to the properties marked with #[AustralEntitySanitize].
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class Sanitizer
{

  /**
   * @var array<string, HtmlSanitizerInterface>
   */
  private array $sanitizers = [];

  /**
   * @var array<class-string, array<string, string>> property => configuration, by class
   */
  private array $propertiesByClass = [];

  /**
   * @param iterable<AustralSanitizerInterface> $sanitizers
   */
  public function __construct(
    #[AutowireIterator('austral_entity.sanitizer_rule')] iterable $sanitizers = []
  )
  {
    foreach($sanitizers as $sanitizer)
    {
      $this->sanitizers[$sanitizer->getName()] = $sanitizer->getSanitizer();
    }
  }

  /**
   * Sanitizes the entity properties and returns the modified values.
   *
   * @param object $entity
   *
   * @return array<string, string> property name => sanitized value (only the changed ones)
   * @throws \InvalidArgumentException if a sanitizer configuration does not exist
   */
  public function sanitize(object $entity): array
  {
    $changed = [];
    $reflectionClass = new \ReflectionClass($entity);
    foreach($this->getSanitizedProperties($reflectionClass) as $propertyName => $config)
    {
      $property = $this->findProperty($reflectionClass, $propertyName);
      if(!$property->isInitialized($entity))
      {
        continue;
      }
      $currentValue = $property->getValue($entity);
      if(!is_string($currentValue) || $currentValue === "")
      {
        continue;
      }
      if(!isset($this->sanitizers[$config]))
      {
        throw new \InvalidArgumentException(sprintf('The sanitizer configuration "%s" does not exist.', $config));
      }
      $sanitizedValue = $this->sanitizers[$config]->sanitize($currentValue);
      if($sanitizedValue !== $currentValue)
      {
        $property->setValue($entity, $sanitizedValue);
        $changed[$propertyName] = $sanitizedValue;
      }
    }
    return $changed;
  }

  /**
   * @param \ReflectionClass<object> $reflectionClass
   *
   * @return array<string, string>
   */
  protected function getSanitizedProperties(\ReflectionClass $reflectionClass): array
  {
    $classname = $reflectionClass->getName();
    if(!array_key_exists($classname, $this->propertiesByClass))
    {
      $properties = [];
      $class = $reflectionClass;
      // Private properties of parent classes are not returned by getProperties()
      do {
        foreach($class->getProperties() as $property)
        {
          if(array_key_exists($property->getName(), $properties))
          {
            continue;
          }
          foreach($property->getAttributes(AustralEntitySanitize::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute)
          {
            $properties[$property->getName()] = $attribute->newInstance()->config;
          }
        }
      } while($class = $class->getParentClass());
      $this->propertiesByClass[$classname] = $properties;
    }
    return $this->propertiesByClass[$classname];
  }

  /**
   * @param \ReflectionClass<object> $reflectionClass
   * @param string $propertyName
   *
   * @return \ReflectionProperty
   */
  protected function findProperty(\ReflectionClass $reflectionClass, string $propertyName): \ReflectionProperty
  {
    $class = $reflectionClass;
    do {
      if($class->hasProperty($propertyName))
      {
        return $class->getProperty($propertyName);
      }
    } while($class = $class->getParentClass());
    throw new \LogicException("Property {$propertyName} not found.");
  }

}
