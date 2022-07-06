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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\EntityManager\EntityManagerInterface;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Event Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerEvent extends Event
{

  const EVENT_CREATE = "austral.entity_manager.create";
  const EVENT_UPDATE = "austral.entity_manager.update";
  const EVENT_DELETE = "austral.entity_manager.delete";
  const EVENT_DUPLICATE = "austral.entity_manager.duplicate";
  const EVENT_PUSH_BEFORE = "austral.entity_manager.push.before";
  const EVENT_PUSH_AFTER = "austral.entity_manager.push.after";

  /**
   * @var EntityInterface
   */
  private EntityInterface $object;

  /**
   * @var EntityInterface|null
   */
  private ?EntityInterface $sourceObject = null;

  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  /**
   * @var string
   */
  private string $name;

  /**
   * @var array
   */
  private array $hydrateValues = array();

  /**
   * @var bool
   */
  private bool $dispatchToFlush = false;

  /**
   * @var \DateTime
   */
  private \DateTime $dateNow;

  /**
   * EntityDuplicateEvent constructor.
   *
   * @param string $name
   * @param EntityManagerInterface $entityManager
   * @param EntityInterface $object
   * @param \DateTime|null $dateNow
   */
  public function __construct(string $name, EntityManagerInterface $entityManager, EntityInterface $object, ?\DateTime $dateNow = null)
  {
    $this->name = $name;
    $this->entityManager = $entityManager;
    $this->object = $object;
    $this->dateNow = $dateNow ? : new \DateTime();
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get entityManager
   * @return EntityManagerInterface
   */
  public function getEntityManager(): EntityManagerInterface
  {
    return $this->entityManager;
  }

  /**
   * @return EntityInterface
   */
  public function getObject(): EntityInterface
  {
    return $this->object;
  }

  /**
   * @return EntityInterface|null
   */
  public function getSourceObject(): ?EntityInterface
  {
    return $this->sourceObject;
  }

  /**
   * @var EntityInterface $sourceObject
   *
   * @return $this
   */
  public function setSourceObject(EntityInterface $sourceObject): EntityManagerEvent
  {
    $this->sourceObject = $sourceObject;
    return $this;
  }

  /**
   * @return array
   */
  public function getHydrateValues(): array
  {
    return $this->hydrateValues;
  }

  /**
   * @param array $hydrateValues
   *
   * @return EntityManagerEvent
   */
  public function setHydrateValues(array $hydrateValues): EntityManagerEvent
  {
    $this->hydrateValues = $hydrateValues;
    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDateNow(): \DateTime
  {
    return $this->dateNow;
  }

  /**
   * @param \DateTime $dateNow
   *
   * @return $this
   */
  public function setDateNow(\DateTime $dateNow): EntityManagerEvent
  {
    $this->dateNow = $dateNow;
    return $this;
  }

  /**
   * @return bool
   */
  public function getDispatchToFlush(): bool
  {
    return $this->dispatchToFlush;
  }

  /**
   * @param bool $dispatchToFlush
   *
   * @return EntityManagerEvent
   */
  public function setDispatchToFlush(bool $dispatchToFlush): EntityManagerEvent
  {
    $this->dispatchToFlush = $dispatchToFlush;
    return $this;
  }

}