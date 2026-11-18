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
 * Sanitizes the (string) value of the property before it is saved to the database,
 * with the named sanitizer configuration (e.g. "default", "html").
 *
 * Example: #[AustralEntitySanitize("html")]
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class AustralEntitySanitize extends AustralEntityAttribute
{
  /**
   * @param string $config name of the sanitizer configuration
   */
  public function __construct(public string $config = 'default')
  {
  }
}
