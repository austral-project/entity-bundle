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

use Austral\Contracts\Entity\EntityInterface;
use Austral\EntityBundle\EntityManager\EntityManager;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Event Entity Manager.
 * @template T of object
 *
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
   * @var EntityInterface<T>
   */
  private EntityInterface $object;

  /**
   * @var EntityInterface<T>|null
   */
  private ?EntityInterface $sourceObject = null;

  /**
   * @var EntityManager<T>
   */
  private EntityManager $entityManager;

  /**
   * @var string
   */
  private string $name;

  /**
   * @var array<string, mixed>
   */
  private array $hydrateValues = [];

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
   * @param EntityManager<T> $entityManager
   * @param EntityInterface<T> $object
   * @param \DateTime|null $dateNow
   */
  public function __construct(string $name, EntityManager $entityManager, EntityInterface $object, ?\DateTime $dateNow = null)
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
   * @return EntityManager<T>
   */
  public function getEntityManager(): EntityManager
  {
    return $this->entityManager;
  }

  /**
   * @return EntityInterface<T>
   */
  public function getObject(): EntityInterface
  {
    return $this->object;
  }

  /**
   * @return EntityInterface<T>|null
   */
  public function getSourceObject(): ?EntityInterface
  {
    return $this->sourceObject;
  }

  /**
   * @param EntityInterface<T> $sourceObject
   *
   * @return $this
   */
  public function setSourceObject(EntityInterface $sourceObject): self
  {
    $this->sourceObject = $sourceObject;
    return $this;
  }

  /**
   * @return array<string, mixed>
   */
  public function getHydrateValues(): array
  {
    return $this->hydrateValues;
  }

  /**
   * @param array<string, mixed> $hydrateValues
   *
   * @return $this
   */
  public function setHydrateValues(array $hydrateValues): self
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
  public function setDateNow(\DateTime $dateNow): self
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
   * @return $this
   */
  public function setDispatchToFlush(bool $dispatchToFlush): self
  {
    $this->dispatchToFlush = $dispatchToFlush;
    return $this;
  }

}