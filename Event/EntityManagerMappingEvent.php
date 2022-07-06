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

use Austral\EntityBundle\EntityManager\EntityManagerInterface;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Event EntityMapping.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerMappingEvent extends Event
{

  const EVENT_NAME = "austral.entity_manager.mapping_association";

  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  /**
   * @var array
   */
  private array $fieldsMapping;

  public function __construct(EntityManagerInterface $entityManager, array $fieldsMapping = array())
  {
    $this->entityManager = $entityManager;
    $this->fieldsMapping = $fieldsMapping;
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
   * @return array
   */
  public function getFieldsMapping(): array
  {
    return $this->fieldsMapping;
  }

  /**
   * @param array $fieldsMapping
   *
   * @return $this
   */
  public function setFieldsMapping(array $fieldsMapping = array()): EntityManagerMappingEvent
  {
    $this->fieldsMapping = $fieldsMapping;
    return $this;
  }

}