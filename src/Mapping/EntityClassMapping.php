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
 * Austral EntityClassMapping.
 *
 * @template T of object
 *
 * @implements EntityClassMappingInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class EntityClassMapping implements EntityClassMappingInterface
{
  /**
   * @var ValueAccessorInterface|null
   */
  protected ?ValueAccessorInterface $valueAccessor = null;

  /**
   * @var EntityMapping<T>|null
   */
  protected ?EntityMapping $entityMapping = null;

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
  public function getObjectValue(EntityInterface $object, string $fieldname): mixed
  {
    return $this->getValueAccessor()->getValue($object, $fieldname);
  }

  /**
   * {@inheritDoc}
   */
  public function setObjectValue(EntityInterface $object, string $fieldname, mixed $value = null): self
  {
    $this->getValueAccessor()->setValue($object, $fieldname, $value);

    return $this;
  }
}
