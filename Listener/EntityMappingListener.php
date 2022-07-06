<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Listener;


use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\EntityAnnotation\EntitiesAnnotations;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\ToolsBundle\Services\Debug;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Annotation Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{

  /**
   * @var Mapping
   */
  protected Mapping $mapping;

  /**
   * @var EntitiesAnnotations
   */
  protected EntitiesAnnotations $entitiesAnnotations;

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * ControllerListener constructor.
   *
   * @param Mapping $mapping
   * @param EntitiesAnnotations $entitiesAnnotations
   * @param EventDispatcherInterface $eventDispatcher
   * @param Debug $debug
   */
  public function __construct(Mapping $mapping, EntitiesAnnotations $entitiesAnnotations, EventDispatcherInterface $eventDispatcher, Debug $debug)
  {
    $this->mapping = $mapping;
    $this->entitiesAnnotations = $entitiesAnnotations;
    $this->debug = $debug;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   */
  public function initEntityAnnotations()
  {
    $this->debug->stopWatchStart("entity-annotation-init", "austral.entity.annotation.listener");
    $australEntityAnnotationEvent = new EntityMappingEvent($this->mapping, $this->entitiesAnnotations);
    $this->eventDispatcher->dispatch($australEntityAnnotationEvent, EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING);
    $this->debug->stopWatchStop("entity-annotation-init");
  }

}
