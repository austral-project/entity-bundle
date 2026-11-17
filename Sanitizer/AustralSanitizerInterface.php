<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Sanitizer;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

interface AustralSanitizerInterface
{
  /**
   * Name of the configuration (e.g. "default", "strict")
   */
  public function getName(): string;

  /**
   * The configured HtmlSanitizer instance
   */
  public function getSanitizer(): HtmlSanitizerInterface;
}
