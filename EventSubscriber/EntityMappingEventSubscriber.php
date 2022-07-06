<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EntityBundle\EventSubscriber;

use Austral\EntityBundle\EntityAnnotation\EntityAnnotations;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EntityAnnotation EventSubscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityMappingEventSubscriber implements EventSubscriberInterface
{

  /**
   * @var string
   */
  protected string $debugContainer = "entity.annotation.event";

  /**
   * ControllerListener constructor.
   *
   */
  public function __construct()
  {
  }


  /**
   * @return array[]
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING     =>  ["initialiseMapping", 1024]
    ];
  }

  /**
   * @param EntityMappingEvent $annotationEvent
   *
   * @return void
   */
  public function initialiseMapping(EntityMappingEvent $annotationEvent)
  {
    /**
     * @var EntityAnnotations $entityAnnotation
     */
    foreach($annotationEvent->getEntitiesAnnotations()->all() as $entityAnnotation)
    {
      if(($entityAnnotation->getClassAnnotations() || $entityAnnotation->getFieldsAnnotations()) && !$annotationEvent->getMapping()->getEntityMapping($entityAnnotation->getClassname()))
      {
        $annotationEvent->getMapping()->addEntityMapping($entityAnnotation->getClassname(), new EntityMapping($entityAnnotation->getClassname(), $entityAnnotation->getSlugger()));
      }
    }
  }

}