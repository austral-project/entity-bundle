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
 * Reads and writes a field value on an entity for the mapping.
 *
 * Implementations must hold no state that cannot be exported (no closures, no services):
 * they are kept in the mapping, which will be cached.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface ValueAccessorInterface
{
  /**
   * @param EntityInterface<*> $object
   */
  public function getValue(EntityInterface $object, string $fieldname): mixed;

  /**
   * @param EntityInterface<*> $object
   */
  public function setValue(EntityInterface $object, string $fieldname, mixed $value): void;
}
