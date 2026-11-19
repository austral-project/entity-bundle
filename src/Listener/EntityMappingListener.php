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

use Austral\EntityBundle\EntityAttributes\EntitiesAttributes;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\ToolsBundle\Services\Debug;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Annotation Listener.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{
  /**
   * @var Mapping<T>
   */
  protected Mapping $mapping;

  /**
   * @var EntitiesAttributes<T>
   */
  protected EntitiesAttributes $entitiesAttributes;

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
   * @param Mapping<T>               $mapping
   * @param EntitiesAttributes<T>    $entitiesAttributes
   * @param EventDispatcherInterface $eventDispatcher
   * @param Debug                    $debug
   */
  public function __construct(
    #[Autowire(service: 'austral.entity.mapping')] Mapping $mapping,
    #[Autowire(service: 'austral.entity.entities-attributes')] EntitiesAttributes $entitiesAttributes,
    #[Autowire(service: 'event_dispatcher')] EventDispatcherInterface $eventDispatcher,
    #[Autowire(service: 'austral.tools.debug')] Debug $debug
  ) {
    $this->mapping = $mapping;
    $this->entitiesAttributes = $entitiesAttributes;
    $this->debug = $debug;
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * initEntityAnnotations
   *
   * @return void
   */
  public function initEntityAnnotations(): void
  {
    $this->debug->stopWatchStart('entity-annotation-init', 'austral.entity.annotation.listener');
    $australEntityAnnotationEvent = new EntityMappingEvent($this->mapping, $this->entitiesAttributes);
    $this->eventDispatcher->dispatch($australEntityAnnotationEvent, EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING);
    $this->debug->stopWatchStop('entity-annotation-init');
  }
}
