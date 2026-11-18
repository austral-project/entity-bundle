<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Fixtures;

use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;

/**
 * Test object using EntityTimestampableTrait (the bundle itself does not use it).
 */
final class TimestampableObject
{
  use EntityTimestampableTrait;
}
