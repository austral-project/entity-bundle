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

use Austral\EntityBundle\Event\QueryBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral EntityRepository Subscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class QueryBuilderEventSubscriber implements EventSubscriberInterface
{
  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
      QueryBuilderEvent::EVENT_CONDITION        =>  ["condition", 1024],
    ];
  }

  /**
   * @param QueryBuilderEvent $queryBuilderEvent
   *
   * @return void
   */
  public function condition(QueryBuilderEvent $queryBuilderEvent)
  {

  }
}