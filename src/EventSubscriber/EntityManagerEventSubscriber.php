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
use Doctrine\ORM\Mapping\FieldMapping;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
   * @var PropertyAccessorInterface|PropertyAccessor
   */
  protected PropertyAccessorInterface $propertyAccessor;

  /**
   * EntityManagerEventSubscriber constructor
   *
   * @param PropertyAccessorInterface|null $propertyAccessor
   */
  public function __construct(?PropertyAccessorInterface $propertyAccessor = null)
  {
    $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
  }

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
    $object = $entityEvent->getObject();
    $object->setIsCreate(true);
    $hydrateValues = $entityEvent->getHydrateValues();
    foreach ($entityEvent->getEntityManager()->getFieldsMapping() as $fieldName => $params)
    {
      if (array_key_exists($fieldName, $hydrateValues))
      {
        $this->propertyAccessor->setValue($object, $fieldName, $hydrateValues[$fieldName]);
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
    $object = $entityEvent->getObject();
    foreach ($entityEvent->getEntityManager()->getFieldsMapping(get_class($object)) as $fieldName => $fieldMapping)
    {
      if (!$fieldMapping instanceof FieldMapping || !in_array($fieldMapping->type, ['string', 'text'], true))
      {
        continue;
      }
      if (!$this->propertyAccessor->isWritable($object, $fieldName))
      {
        continue;
      }
      if (true === $fieldMapping->id)
      {
        $this->propertyAccessor->setValue($object, $fieldName, Uuid::uuid4()->toString());
      }
      elseif (true === $fieldMapping->unique && $this->propertyAccessor->isReadable($object, $fieldName))
      {
        $value = $this->propertyAccessor->getValue($object, $fieldName);
        $this->propertyAccessor->setValue($object, $fieldName, "{$value} - ".AustralTools::random(8));
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
