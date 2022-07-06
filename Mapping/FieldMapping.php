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
use Closure;
use Exception;

/**
 * Austral FieldMapping.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
abstract class FieldMapping implements FieldMappingInterface
{

  /**
   * @var string
   */
  protected string $fieldname;

  /**
   * @var Closure|null
   */
  protected ?Closure $getterObjectValueFonction = null;

  /**
   * @var Closure|null
   */
  protected ?Closure $setterObjectValueFonction = null;

  /**
   * @var EntityMapping
   */
  protected EntityMapping $entityMapping;

  /**
   * @param EntityMapping $entityMapping
   *
   * @return $this
   */
  public function setEntityMapping(EntityMapping $entityMapping): FieldMappingInterface
  {
    $this->entityMapping = $entityMapping;
    return $this;
  }

  /**
   * @return string
   */
  public function getClassname(): string
  {
    return get_class($this);
  }

  /**
   * @return string|null
   */
  public function getFieldname(): ?string
  {
    return $this->fieldname;
  }

  /**
   * @param string $fieldname
   *
   * @return FieldMappingInterface
   */
  public function setFieldname(string $fieldname): FieldMappingInterface
  {
    $this->fieldname = $fieldname;
    return $this;
  }

  /**
   * @param Closure $getterObjectValueFonction
   *
   * @return $this
   */
  public function addGetterObjectValueFonction(Closure $getterObjectValueFonction): FieldMappingInterface
  {
    $this->getterObjectValueFonction = $getterObjectValueFonction;
    return $this;
  }

  /**
   * @param Closure $setterObjectValueFonction
   *
   * @return $this
   */
  public function addSetterObjectValueFonction(Closure $setterObjectValueFonction): FieldMappingInterface
  {
    $this->setterObjectValueFonction = $setterObjectValueFonction;
    return $this;
  }

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   *
   * @return mixed
   * @throws Exception
   */
  public function getObjectValue(EntityInterface $object, string $fieldname = null)
  {
    if($this->getterObjectValueFonction instanceof Closure)
    {
      return $this->getterObjectValueFonction->call($this, $object, $fieldname ? : $this->getFieldname());
    }
    return $object->getValueByFieldname($fieldname ? :$this->getFieldname());
  }

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   * @param null $value
   *
   * @return $this
   * @throws Exception
   */
  public function setObjectValue(EntityInterface $object, string $fieldname = null, $value = null): FieldMappingInterface
  {
    if($this->setterObjectValueFonction instanceof Closure)
    {
      $this->setterObjectValueFonction->call($this, $object, $fieldname ? : $this->getFieldname(), $value);
    }
    $object->setValueByFieldname($fieldname ? :$this->getFieldname(), $value);
    return $this;
  }


}