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
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
final Class EntityMapping
{

  /**
   * @var Mapping
   */
  protected Mapping $mapping;

  /**
   * @var string
   */
  public string $slugger;

  /**
   * @var string
   */
  public string $entityClass;

  /**
   * @var array
   */
  protected array $fieldsMapping = array();

  /**
   * @var array
   */
  protected array $entityClassMapping = array();

  /**
   * @var array
   */
  protected array $fieldsMappingByClass = array();


  /**
   * Constructor.
   */
  public function __construct(string $entityClass, string $slugger, array $fieldsMapping = array())
  {
    $this->entityClass = $entityClass;
    $this->slugger = $slugger;
    /** @var array $fieldMappingsByClass */
    foreach($fieldsMapping as $fieldMappingsByClass)
    {
      /** @var FieldMappingInterface $fieldMapping */
      foreach($fieldMappingsByClass as $fieldMapping)
      {
        $fieldMapping->setEntityMapping($this);
      }
    }
    $this->fieldsMapping = $fieldsMapping;
  }

  /**
   * @param Mapping $mapping
   *
   * @return $this
   */
  public function setMapping(Mapping $mapping): EntityMapping
  {
    $this->mapping = $mapping;
    return $this;
  }

  /**
   * @param EntityClassMappingInterface $entityClassMapping
   *
   * @return EntityMapping
   */
  public function addEntityClassMapping(EntityClassMappingInterface $entityClassMapping): EntityMapping
  {
    $entityClassMapping->setEntityMapping($this);
    $this->entityClassMapping[$entityClassMapping->getClassname()] = $entityClassMapping;
    return $this;
  }

  /**
   * @param string $entityClassMappingClass
   *
   * @return ?EntityClassMappingInterface
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
   * @param string $fieldname
   * @param FieldMappingInterface $fieldMapping
   *
   * @return EntityMapping
   */
  public function addFieldMapping(string $fieldname, FieldMappingInterface $fieldMapping): EntityMapping
  {
    $fieldMapping->setFieldname($fieldname);
    $fieldMapping->setEntityMapping($this);
    $this->fieldsMapping[$fieldMapping->getClassname()][$fieldname] = $fieldMapping;
    $this->fieldsMappingByClass[$fieldname][] = $fieldMapping->getClassname();
    return $this;
  }

  /**
   * @param string $fieldMappingClass
   * @param string $fieldname
   *
   * @return ?FieldMappingInterface
   */
  public function getFieldsMappingByFieldname(string $fieldMappingClass, string $fieldname): ?FieldMappingInterface
  {
    return array_key_exists($fieldname, $this->getFieldsMappingByClass($fieldMappingClass)) ? $this->getFieldsMappingByClass($fieldMappingClass)[$fieldname] : null;
  }

  /**
   * @param string $fieldMappingClass
   *
   * @return array
   */
  public function getFieldsMappingByClass(string $fieldMappingClass): array
  {
    return array_key_exists($fieldMappingClass, $this->getFieldsMapping()) ? $this->getFieldsMapping()[$fieldMappingClass] : array();
  }

  /**
   * @param string $fieldname
   *
   * @return array
   */
  public function getAllFieldsMappingClassByFieldname(string $fieldname): array
  {
    $allFieldsMappingClassByFieldname = array();
    if(array_key_exists($fieldname, $this->fieldsMappingByClass))
    {
      foreach($this->fieldsMappingByClass[$fieldname] as $fieldMappingClass)
      {
        $allFieldsMappingClassByFieldname[] = $this->getFieldsMappingByClass($fieldMappingClass)[$fieldname];
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
    if(array_key_exists($fieldname, $this->fieldsMappingByClass))
    {
      return in_array($fieldMappingClass, $this->fieldsMappingByClass[$fieldname]);
    }
    return false;
  }


  /**
   * @return array
   */
  public function getFieldsMapping(): array
  {
    return $this->fieldsMapping;
  }

}
