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

/**
 * Austral EntityMapping.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
final class EntityMapping
{
  /**
   * @var Mapping<T>|null
   */
  private ?Mapping $mapping = null;

  /**
   * @var string
   */
  public string $slugger;

  /**
   * @var class-string<T>
   */
  public string $entityClass;

  /**
   * @var array<class-string, array<string, FieldMappingInterface<T>>>|array<null>
   */
  private array $fieldsMapping = [];

  /**
   * @var array<class-string, EntityClassMappingInterface<T>>|array<null>
   */
  private array $entityClassMapping = [];

  /**
   * @var array<string, array<int, class-string>>|array<null>
   */
  private array $fieldsMappingByClass = [];

  /**
   * EntityMapping constructor
   *
   * @param class-string<T>      $entityClass
   * @param string               $slugger
   * @param array<string, mixed> $fieldsMapping
   */
  public function __construct(string $entityClass, string $slugger, array $fieldsMapping = [])
  {
    $this->entityClass = $entityClass;
    $this->slugger = $slugger;
    /** @var array<string, FieldMappingInterface<T>> $fieldMappingsByClass */
    foreach ($fieldsMapping as $fieldMappingsByClass)
    {
      foreach ($fieldMappingsByClass as $fieldMapping)
      {
        $fieldMapping->setEntityMapping($this);
      }
    }
    $this->fieldsMapping = $fieldsMapping;
  }

  /**
   * @param Mapping<T> $mapping
   *
   * @return $this
   */
  public function setMapping(Mapping $mapping): self
  {
    $this->mapping = $mapping;

    return $this;
  }

  /**
   * @return Mapping<T>|null
   */
  public function getMapping(): ?Mapping
  {
    return $this->mapping;
  }

  /**
   * @param EntityClassMappingInterface<T> $entityClassMapping
   *
   * @return $this
   */
  public function addEntityClassMapping(EntityClassMappingInterface $entityClassMapping): self
  {
    $entityClassMapping->setEntityMapping($this);
    $this->entityClassMapping[$entityClassMapping->getClassname()] = $entityClassMapping;

    return $this;
  }

  /**
   * @param string $entityClassMappingClass
   *
   * @return ?EntityClassMappingInterface<T>
   */
  public function getEntityClassMapping(string $entityClassMappingClass): ?EntityClassMappingInterface
  {
    return $this->hasEntityClassMapping($entityClassMappingClass) ? $this->entityClassMapping[$entityClassMappingClass] : null;
  }

  /**
   * @param string $entityClassMappingClass
   *
   * @return bool
   */
  public function hasEntityClassMapping(string $entityClassMappingClass): bool
  {
    return array_key_exists($entityClassMappingClass, $this->entityClassMapping);
  }

  /**
   * @param string                   $fieldname
   * @param FieldMappingInterface<T> $fieldMapping
   *
   * @return $this
   */
  public function addFieldMapping(string $fieldname, FieldMappingInterface $fieldMapping): self
  {
    $fieldMapping->setFieldname($fieldname);
    $fieldMapping->setEntityMapping($this);
    $this->fieldsMapping[$fieldMapping->getClassname()][$fieldname] = $fieldMapping;
    $this->fieldsMappingByClass[$fieldname][] = $fieldMapping->getClassname();

    return $this;
  }

  /**
   * @param class-string $fieldMappingClass
   * @param string       $fieldname
   *
   * @return ?FieldMappingInterface<T>
   */
  public function getFieldsMappingByFieldname(string $fieldMappingClass, string $fieldname): ?FieldMappingInterface
  {
    return array_key_exists($fieldname, $this->getFieldsMappingByClass($fieldMappingClass)) ? $this->getFieldsMappingByClass($fieldMappingClass)[$fieldname] : null;
  }

  /**
   * @param class-string $fieldMappingClass
   *
   * @return array<string, FieldMappingInterface<T>>
   */
  public function getFieldsMappingByClass(string $fieldMappingClass): array
  {
    return array_key_exists($fieldMappingClass, $this->getFieldsMapping()) && null !== $this->getFieldsMapping()[$fieldMappingClass] ? $this->getFieldsMapping()[$fieldMappingClass] : [];
  }

  /**
   * @param string $fieldname
   *
   * @return list<FieldMappingInterface<T>>
   */
  public function getAllFieldsMappingClassByFieldname(string $fieldname): array
  {
    $allFieldsMappingClassByFieldname = [];
    if (array_key_exists($fieldname, $this->fieldsMappingByClass) && null != $this->fieldsMappingByClass[$fieldname])
    {
      foreach ($this->fieldsMappingByClass[$fieldname] as $fieldMappingClass)
      {
        if ($fieldMapping = $this->getFieldsMappingByFieldname($fieldMappingClass, $fieldname))
        {
          $allFieldsMappingClassByFieldname[] = $fieldMapping;
        }
      }
    }

    return $allFieldsMappingClassByFieldname;
  }

  /**
   * @param string $fieldname
   * @param string $fieldMappingClass
   *
   * @return bool
   */
  public function hasFieldnameMappingClass(string $fieldname, string $fieldMappingClass): bool
  {
    if (array_key_exists($fieldname, $this->fieldsMappingByClass) && null != $this->fieldsMappingByClass[$fieldname])
    {
      return in_array($fieldMappingClass, $this->fieldsMappingByClass[$fieldname]);
    }

    return false;
  }

  /**
   * @return array<class-string, array<string, FieldMappingInterface<T>>>|array<null>
   */
  public function getFieldsMapping(): array
  {
    return $this->fieldsMapping;
  }
}
