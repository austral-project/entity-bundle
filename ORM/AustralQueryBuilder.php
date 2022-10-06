<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\ORM;

use Austral\EntityBundle\Event\QueryBuilderEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder as BaseQueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Austral Event EntityRepository.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class AustralQueryBuilder extends BaseQueryBuilder
{
  /**
   * @var EventDispatcherInterface|null
   */
  private ?EventDispatcherInterface $dispatcher;

  /**
   * @var string
   */
  private string $entityClassname;

  /**
   * Initializes a new <tt>QueryBuilder</tt> that uses the given <tt>EntityManager</tt>.
   *
   * @param EntityManagerInterface $em The EntityManager to use.
   * @param string $entityClassname
   * @param EventDispatcherInterface|null $dispatcher
   */
  public function __construct(EntityManagerInterface $em, string $entityClassname, ?EventDispatcherInterface $dispatcher = null)
  {
    parent::__construct($em);
    $this->entityClassname = $entityClassname;
    $this->dispatcher = $dispatcher;
  }


  /**
   * @param QueryConditionInterface $queryCondition
   *
   * @return AustralQueryBuilder
   * @throws \Exception
   */
  public function queryCondition(QueryConditionInterface $queryCondition): AustralQueryBuilder
  {
    if($this->dispatcher)
    {
      $entityRepositoryEvent = new QueryBuilderEvent($this, $queryCondition);
      $this->dispatcher->dispatch($entityRepositoryEvent, QueryBuilderEvent::EVENT_CONDITION);
    }
    return $this;
  }

  /**
   * @return string
   */
  public function getEntityClassname(): string
  {
    return $this->entityClassname;
  }

  /**
   * @param string $entityClassname
   *
   * @return AustralQueryBuilder
   */
  public function setEntityClassname(string $entityClassname): AustralQueryBuilder
  {
    $this->entityClassname = $entityClassname;
    return $this;
  }

}