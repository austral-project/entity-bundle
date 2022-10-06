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
interface QueryConditionInterface
{
  /** @var string  */
  CONST WHERE = "where";

  /** @var string  */
  CONST AND_WHERE = "andWhere";

  /** @var string  */
  CONST OR_WHERE = "orWhere";

  CONST WHERE_ENABLED = array(
    self::WHERE,
    self::AND_WHERE,
    self::OR_WHERE
  );

}
