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
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface QueryConditionInterface
{
  /** @var string */
  public const WHERE = 'where';

  /** @var string */
  public const AND_WHERE = 'andWhere';

  /** @var string */
  public const OR_WHERE = 'orWhere';

  public const WHERE_ENABLED = [
    self::WHERE,
    self::AND_WHERE,
    self::OR_WHERE,
  ];
}
