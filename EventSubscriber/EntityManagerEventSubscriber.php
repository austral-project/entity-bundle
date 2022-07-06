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

use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Event\EntityManagerMappingEvent;

use Austral\ToolsBundle\AustralTools;

use Ramsey\Uuid\Uuid;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function Symfony\Component\String\u;

/**
 * Austral Entity Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerEventSubscriber implements EventSubscriberInterface
{
  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
      EntityManagerEvent::EVENT_CREATE        =>  ["create", 1024],
      EntityManagerEvent::EVENT_DELETE        =>  ["delete", 1024],
      EntityManagerEvent::EVENT_UPDATE        =>  ["update", 1024],
      EntityManagerEvent::EVENT_DUPLICATE     =>  ["duplicate", 1024],
      EntityManagerEvent::EVENT_PUSH_BEFORE   =>  ["pushBefore", 1024],
      EntityManagerEvent::EVENT_PUSH_AFTER    =>  ["pushAfter", 1024],
      EntityManagerMappingEvent::EVENT_NAME   =>  ["mapping", 1024],
    ];
  }

  /**
   * @param EntityManagerEvent $entityEvent
   */
  public function create(EntityManagerEvent $entityEvent)
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
      ->enableExceptionOnInvalidIndex()
      ->getPropertyAccessor();

    $object = $entityEvent->getObject();
    $hydrateValues = $entityEvent->getHydrateValues();
    foreach($entityEvent->getEntityManager()->getFieldsMapping() as $fieldName => $params)
    {
      if(array_key_exists($fieldName, $hydrateValues))
      {
        $propertyAccessor->setValue($object, $fieldName, $hydrateValues[$fieldName]);
      }
    }
  }

  /**
   * @param EntityManagerEvent $entityEvent
   */
  public function delete(EntityManagerEvent $entityEvent)
  {

  }

  /**
   * @param EntityManagerEvent $entityEvent
   */
  public function update(EntityManagerEvent $entityEvent)
  {

  }

  /**
   * @param EntityManagerEvent $entityEvent
   *
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityEvent)
  {
    foreach($entityEvent->getEntityManager()->getFieldsMapping(get_class($entityEvent->getObject())) as $fieldName => $params)
    {
      $setterFunction = u(u($fieldName)->camel()->title())->ensureStart('set')->__toString();
      $getterFunction = u(u($fieldName)->camel()->title())->ensureStart('get')->__toString();

      $valueHasChange = false;
      $newValue = null;
      if(array_key_exists("id", $params))
      {
        if($params['type'] == "string" || $params['type'] == "text")
        {
          $valueHasChange = true;
          $newValue = Uuid::uuid4()->__toString();
        }
        else
        {
          $valueHasChange = true;
        }
      }
      elseif($params['unique'] === true)
      {
        if($params['type'] == "string" || $params['type'] == "text")
        {
          $valueHasChange = true;
          $newValue = "{$entityEvent->getObject()->$getterFunction()} - ".AustralTools::random(8);
        }
      }
      if($valueHasChange)
      {
        if(method_exists($entityEvent->getObject(), $setterFunction))
        {
          $entityEvent->getObject()->$setterFunction($newValue);
        }
      }
    }
  }

  /**
   * @param EntityManagerMappingEvent $mappingAssociationEvent
   */
  public function mapping(EntityManagerMappingEvent $mappingAssociationEvent)
  {
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   */
  public function pushBefore(EntityManagerEvent $entityManagerEvent)
  {
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   */
  public function pushAfter(EntityManagerEvent $entityManagerEvent)
  {
  }

}