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

use Austral\EntityBundle\EntityAttributes\EntityAttributes;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EntityAnnotation EventSubscriber.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class EntityMappingEventSubscriber implements EventSubscriberInterface
{
  /**
   * @var string
   */
  protected string $debugContainer = 'entity.attribute.event';

  /**
   * ControllerListener constructor.
   */
  public function __construct()
  {
  }

  /**
   * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EntityMappingEvent::EVENT_AUSTRAL_ENTITY_INITIALISE_MAPPING => ['initialiseMapping', 1024],
    ];
  }

  /**
   * @param EntityMappingEvent<T> $attributeEvent
   *
   * @return void
   */
  public function initialiseMapping(EntityMappingEvent $attributeEvent): void
  {
    /**
     * @var EntityAttributes<T> $entityAttributes
     */
    foreach ($attributeEvent->getEntitiesAttributes()->all() as $entityAttributes)
    {
      if (($entityAttributes->getClassAttributes() || $entityAttributes->getFieldsAttributes()) && !$attributeEvent->getMapping()->getEntityMapping($entityAttributes->getClassname()))
      {
        $entityMapping = new EntityMapping($entityAttributes->getClassname(), $entityAttributes->getSlugger());
        $attributeEvent
          ->getMapping()
          ->addEntityMapping($entityAttributes->getClassname(), $entityMapping);
      }
    }
  }
}
