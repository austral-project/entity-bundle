<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Mapping\ValueAccessor;

use Austral\Contracts\Entity\EntityInterface;

/**
 * Default accessor: delegates to the entity itself.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
final class EntityValueAccessor implements ValueAccessorInterface
{
  /**
   * {@inheritDoc}
   */
  public function getValue(EntityInterface $object, string $fieldname): mixed
  {
    return $object->getValueByFieldname($fieldname);
  }

  /**
   * {@inheritDoc}
   */
  public function setValue(EntityInterface $object, string $fieldname, mixed $value = null): void
  {
    $object->setValueByFieldname($fieldname, $value);
  }
}
