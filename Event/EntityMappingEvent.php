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
use Austral\EntityBundle\EntityAnnotation\EntitiesAnnotations;
use Austral\EntityBundle\Mapping\Mapping;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Http Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
class EntityMappingEvent extends Event
{

  const EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING = "austral.event.entity.initialise.mapping";

  /**
   * @var Mapping
   */
  private Mapping $mapping;

  /**
   * @var EntitiesAnnotations
   */
  private EntitiesAnnotations $entitiesAnnotations;


  /**
   * @param Mapping $mapping
   * @param EntitiesAnnotations $entitiesAnnotations
   */
  public function __construct(Mapping $mapping, EntitiesAnnotations $entitiesAnnotations)
  {
    $this->mapping = $mapping;
    $this->entitiesAnnotations = $entitiesAnnotations;
  }

  /**
   * @return Mapping
   */
  public function getMapping(): Mapping
  {
    return $this->mapping;
  }

  /**
   * @param Mapping $mapping
   *
   * @return EntityMappingEvent
   */
  public function setMapping(Mapping $mapping): EntityMappingEvent
  {
    $this->mapping = $mapping;
    return $this;
  }

  /**
   * @return EntitiesAnnotations
   */
  public function getEntitiesAnnotations (): EntitiesAnnotations
  {
    return $this->entitiesAnnotations;
  }

  /**
   * @param EntitiesAnnotations $entitiesAnnotations
   *
   * @return EntityMappingEvent
   */
  public function setEntitiesAnnotations (EntitiesAnnotations $entitiesAnnotations): EntityMappingEvent
  {
    $this->entitiesAnnotations = $entitiesAnnotations;
    return $this;
  }

}