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

use Austral\EntityBundle\ORM\AustralQueryBuilder;
use Austral\EntityBundle\ORM\QueryConditionInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Event EntityRepository.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class QueryBuilderEvent extends Event
{
  public const EVENT_CONDITION = 'austral.event.entity.query_builder.condition';

  /**
   * @var AustralQueryBuilder<T>
   */
  private AustralQueryBuilder $queryBuilder;

  /**
   * @var QueryConditionInterface
   */
  private QueryConditionInterface $queryCondition;

  /**
   * EntityRepositoryEvent constructor.
   *
   * @param AustralQueryBuilder<T>  $queryBuilder
   * @param QueryConditionInterface $queryCondition
   */
  public function __construct(AustralQueryBuilder $queryBuilder, QueryConditionInterface $queryCondition)
  {
    $this->queryBuilder = $queryBuilder;
    $this->queryCondition = $queryCondition;
  }

  /**
   * @return AustralQueryBuilder<T>
   */
  public function getQueryBuilder(): AustralQueryBuilder
  {
    return $this->queryBuilder;
  }

  /**
   * @param AustralQueryBuilder<T> $queryBuilder
   *
   * @return $this
   */
  public function setQueryBuilder(AustralQueryBuilder $queryBuilder): self
  {
    $this->queryBuilder = $queryBuilder;

    return $this;
  }

  /**
   * @return QueryConditionInterface
   */
  public function getQueryCondition(): QueryConditionInterface
  {
    return $this->queryCondition;
  }

  /**
   * @param QueryConditionInterface $queryCondition
   *
   * @return $this
   */
  public function setQueryCondition(QueryConditionInterface $queryCondition): self
  {
    $this->queryCondition = $queryCondition;

    return $this;
  }
}
