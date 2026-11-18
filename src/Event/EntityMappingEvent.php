<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Event;

use Austral\EntityBundle\EntityAttributes\EntitiesAttributes;
use Austral\EntityBundle\Mapping\Mapping;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Http Event.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
class EntityMappingEvent extends Event
{
  public const EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING = 'austral.event.entity.initialise.mapping';

  /**
   * @var Mapping<T>
   */
  private Mapping $mapping;

  /**
   * @var EntitiesAttributes<T>
   */
  private EntitiesAttributes $entitiesAttributes;

  /**
   * @param Mapping<T>            $mapping
   * @param EntitiesAttributes<T> $entitiesAttributes
   */
  public function __construct(Mapping $mapping, EntitiesAttributes $entitiesAttributes)
  {
    $this->mapping = $mapping;
    $this->entitiesAttributes = $entitiesAttributes;
  }

  /**
   * @return Mapping<T>
   */
  public function getMapping(): Mapping
  {
    return $this->mapping;
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
   * @return EntitiesAttributes<T>
   */
  public function getEntitiesAttributes(): EntitiesAttributes
  {
    return $this->entitiesAttributes;
  }

  /**
   * @param EntitiesAttributes<T> $entitiesAttributes
   *
   * @return $this
   */
  public function setEntitiesAttributes(EntitiesAttributes $entitiesAttributes): self
  {
    $this->entitiesAttributes = $entitiesAttributes;

    return $this;
  }
}
