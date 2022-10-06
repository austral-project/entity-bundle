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

/**
 * Austral Interface QueryCondition.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class QueryCondition implements QueryConditionInterface
{

  protected ?string $alias = null;

  /**
   * @var string
   */
  protected string $conditionType;

  /**
   * @param string $conditionType
   * @param string|null $alias
   *
   * @throws \Exception
   */
  public function __construct(string $conditionType = QueryConditionInterface::WHERE, ?string $alias = null)
  {
    if(!in_array($conditionType, QueryConditionInterface::WHERE_ENABLED))
    {
      throw new \Exception("{$conditionType} is not enabled for this function");
    }
    $this->conditionType = $conditionType;
    $this->alias = $alias;
  }

  /**
   * @return string
   */
  public function getConditionType(): string
  {
    return $this->conditionType;
  }

  /**
   * @param string $conditionType
   *
   * @return QueryCondition
   */
  public function setConditionType(string $conditionType): QueryCondition
  {
    $this->conditionType = $conditionType;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getAlias(): ?string
  {
    return $this->alias;
  }

  /**
   * @param string|null $alias
   *
   * @return QueryCondition
   */
  public function setAlias(?string $alias): QueryCondition
  {
    $this->alias = $alias;
    return $this;
  }

}
