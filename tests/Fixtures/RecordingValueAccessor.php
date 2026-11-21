<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Fixtures;

use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\Mapping\ValueAccessor\ValueAccessorInterface;

/**
 * Value accessor that records every call and never touches the entity.
 */
final class RecordingValueAccessor implements ValueAccessorInterface
{
  /**
   * @var list<array{string, string, mixed}>
   */
  public array $calls = [];

  /**
   * @param mixed $returnedValue
   */
  public function __construct(private readonly mixed $returnedValue = 'from-accessor')
  {
  }

  /**
   * {@inheritDoc}
   */
  public function getValue(EntityInterface $object, string $fieldname): mixed
  {
    $this->calls[] = ['get', $fieldname, null];

    return $this->returnedValue;
  }

  /**
   * {@inheritDoc}
   */
  public function setValue(EntityInterface $object, string $fieldname, mixed $value = null): void
  {
    $this->calls[] = ['set', $fieldname, $value];
  }
}
