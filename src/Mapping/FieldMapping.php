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
use Austral\EntityBundle\Mapping\ValueAccessor\EntityValueAccessor;
use Austral\EntityBundle\Mapping\ValueAccessor\ValueAccessorInterface;

/**
 * Austral FieldMapping.
 *
 * @template T of object
 *
 * @implements FieldMappingInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class FieldMapping implements FieldMappingInterface
{
  /**
   * @var string|null
   */
  protected ?string $fieldname = null;

  /**
   * @var EntityMapping<T>|null
   */
  protected ?EntityMapping $entityMapping = null;

  /**
   * @var ValueAccessorInterface|null
   */
  protected ?ValueAccessorInterface $valueAccessor = null;

  /**
   * {@inheritDoc}
   */
  public function setEntityMapping(EntityMapping $entityMapping): self
  {
    $this->entityMapping = $entityMapping;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getClassname(): string
  {
    return get_class($this);
  }

  /**
   * {@inheritDoc}
   */
  public function getFieldname(): ?string
  {
    return $this->fieldname;
  }

  /**
   * {@inheritDoc}
   */
  public function setFieldname(string $fieldname): self
  {
    $this->fieldname = $fieldname;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setValueAccessor(ValueAccessorInterface $valueAccessor): self
  {
    $this->valueAccessor = $valueAccessor;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getValueAccessor(): ValueAccessorInterface
  {
    return $this->valueAccessor ??= new EntityValueAccessor();
  }

  /**
   * {@inheritDoc}
   */
  public function getObjectValue(EntityInterface $object, ?string $fieldname = null): mixed
  {
    $fieldname = $fieldname ?: $this->getFieldname();

    return $fieldname ? $this->getValueAccessor()->getValue($object, $fieldname) : null;
  }

  /**
   * {@inheritDoc}
   */
  public function setObjectValue(EntityInterface $object, ?string $fieldname = null, mixed $value = null): self
  {
    $fieldname = $fieldname ?: $this->getFieldname();
    if ($fieldname)
    {
      $this->getValueAccessor()->setValue($object, $fieldname, $value);
    }

    return $this;
  }
}
