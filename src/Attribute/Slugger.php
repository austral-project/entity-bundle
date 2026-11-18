<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Attribute;

/**
 * Class Slugger
 *
 * @Attribute
 *
 * @Target("TARGET_CLASS")
 *
 * @author  Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Slugger
{
  /**
   * @param string|null $slugger
   */
  public function __construct(public ?string $slugger = null)
  {
  }
}
