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
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class EntityManagerEventSubscriber implements EventSubscriberInterface
{
  /**
   * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
   */
  public static function getSubscribedEvents(): array
  {
    return [
      EntityManagerEvent::EVENT_CREATE => ['create', 1024],
      EntityManagerEvent::EVENT_DELETE => ['delete', 1024],
      EntityManagerEvent::EVENT_UPDATE => ['update', 1024],
      EntityManagerEvent::EVENT_DUPLICATE => ['duplicate', 1024],
      EntityManagerEvent::EVENT_PUSH_BEFORE => ['pushBefore', 1024],
      EntityManagerEvent::EVENT_PUSH_AFTER => ['pushAfter', 1024],
      EntityManagerMappingEvent::EVENT_NAME => ['mapping', 1024],
    ];
  }

  /**
   * @param EntityManagerEvent<T> $entityEvent
   */
  public function create(EntityManagerEvent $entityEvent): void
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
      ->enableExceptionOnInvalidIndex()
      ->getPropertyAccessor();

    $object = $entityEvent->getObject();
    $object->setIsCreate(true);
    $hydrateValues = $entityEvent->getHydrateValues();
    foreach ($entityEvent->getEntityManager()->getFieldsMapping() as $fieldName => $params)
    {
      if (array_key_exists($fieldName, $hydrateValues))
      {
        $propertyAccessor->setValue($object, $fieldName, $hydrateValues[$fieldName]);
      }
    }
  }

  /**
   * @param EntityManagerEvent<T> $entityEvent
   */
  public function delete(EntityManagerEvent $entityEvent): void
  {
  }

  /**
   * @param EntityManagerEvent<T> $entityEvent
   */
  public function update(EntityManagerEvent $entityEvent): void
  {
  }

  /**
   * @param EntityManagerEvent<T> $entityEvent
   *
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityEvent): void
  {
    foreach ($entityEvent->getEntityManager()->getFieldsMapping(get_class($entityEvent->getObject())) as $fieldName => $params)
    {
      $setterFunction = u(u($fieldName)->camel()->title())->ensureStart('set')->__toString();
      $getterFunction = u(u($fieldName)->camel()->title())->ensureStart('get')->__toString();

      $valueHasChange = false;
      $newValue = null;
      if (array_key_exists('id', $params))
      {
        if ('string' == $params['type'] || 'text' == $params['type'])
        {
          $valueHasChange = true;
          $newValue = Uuid::uuid4()->__toString();
        }
        else
        {
          $valueHasChange = true;
        }
      }
      elseif (true === $params['unique'])
      {
        if ('string' == $params['type'] || 'text' == $params['type'])
        {
          $valueHasChange = true;
          $newValue = "{$entityEvent->getObject()->$getterFunction()} - ".AustralTools::random(8);
        }
      }
      if ($valueHasChange)
      {
        if (method_exists($entityEvent->getObject(), $setterFunction))
        {
          $entityEvent->getObject()->$setterFunction($newValue);
        }
      }
    }
  }

  /**
   * @param EntityManagerMappingEvent<T> $mappingAssociationEvent
   */
  public function mapping(EntityManagerMappingEvent $mappingAssociationEvent): void
  {
  }

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   */
  public function pushBefore(EntityManagerEvent $entityManagerEvent): void
  {
  }

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   */
  public function pushAfter(EntityManagerEvent $entityManagerEvent): void
  {
  }
}
