<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityManager;


use Austral\EntityBundle\Event\EntityManagerEvent;

/**
 * Austral Interface EntityManager.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityManagerInterface
{

  /**
   * @return string
   */
  public function getClass(): string;

  /**
   * @param string $class
   *
   * @return $this
   */
  public function setClass(string $class): EntityManagerInterface;

  /**
   * @param $event
   * @param string $eventName
   */
  public function dispatch($event, string $eventName);

  /**
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @return $this
   */
  public function addEntitytManagerEvent(EntityManagerEvent $entityManagerEvent): EntityManagerInterface;


}
