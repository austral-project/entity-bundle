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

/**
 * Austral EntityClassMapping.
 * @template T of object
 *
 * @implements EntityClassMappingInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
abstract class EntityClassMapping implements EntityClassMappingInterface
{

  /**
   * @var (\Closure(EntityInterface<T>, string): mixed)|null
   */
  protected ?\Closure $getterObjectValueFonction = null;

  /**
   * @var (\Closure(EntityInterface<T>, string, mixed): mixed)|null
   */
  protected ?\Closure $setterObjectValueFonction = null;

  /**
   * @var EntityMapping<T>|null
   */
  protected ?EntityMapping $entityMapping = null;

  /**
   * @inheritDoc
   */
  public function setEntityMapping(EntityMapping $entityMapping): EntityClassMappingInterface
  {
    $this->entityMapping = $entityMapping;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getClassname(): string
  {
    return get_class($this);
  }

  /**
   * @inheritDoc
   */
  public function addGetterObjectValueFonction(\Closure $getterObjectValueFonction): self
  {
    $this->getterObjectValueFonction = $getterObjectValueFonction;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function addSetterObjectValueFonction(\Closure $setterObjectValueFonction): self
  {
    $this->setterObjectValueFonction = $setterObjectValueFonction;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getObjectValue(EntityInterface $object, string $fieldname): mixed
  {
    if($this->getterObjectValueFonction instanceof \Closure)
    {
      return $this->getterObjectValueFonction->call($this, $object, $fieldname);
    }
    return $object->getValueByFieldname($fieldname);
  }

  /**
   * @inheritDoc
   */
  public function setObjectValue(EntityInterface $object, string $fieldname, mixed $value = null): EntityClassMappingInterface
  {
    if($this->setterObjectValueFonction instanceof \Closure)
    {
      $this->setterObjectValueFonction->call($this, $object, $fieldname, $value);
    }
    $object->setValueByFieldname($fieldname, $value);
    return $this;
  }


}