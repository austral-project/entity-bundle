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

use Austral\EntityBundle\EntityManager\EntityManager;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Event EntityMapping.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerMappingEvent extends Event
{

  const EVENT_NAME = "austral.entity_manager.mapping_association";

  /**
   * @var EntityManager<T>
   */
  private EntityManager $entityManager;

  /**
   * @var array<string, mixed>
   */
  private array $fieldsMapping;

  /**
   * EntityManagerMappingEvent constructor
   *
   * @param EntityManager<T> $entityManager
   * @param array<string, mixed> $fieldsMapping
   */
  public function __construct(EntityManager $entityManager, array $fieldsMapping = [])
  {
    $this->entityManager = $entityManager;
    $this->fieldsMapping = $fieldsMapping;
  }

  /**
   * Get entityManager
   * @return EntityManager<object>
   */
  public function getEntityManager(): EntityManager
  {
    return $this->entityManager;
  }

  /**
   * @return array<string, mixed>
   */
  public function getFieldsMapping(): array
  {
    return $this->fieldsMapping;
  }

  /**
   * @param array<string, mixed> $fieldsMapping
   *
   * @return $this
   */
  public function setFieldsMapping(array $fieldsMapping = []): self
  {
    $this->fieldsMapping = $fieldsMapping;
    return $this;
  }

}