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

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class AustralDefaultSanitizer implements AustralSanitizerInterface
{
  public function getName(): string
  {
    return 'default';
  }

  public function getSanitizer(): HtmlSanitizerInterface
  {
    $config = new HtmlSanitizerConfig()->allowSafeElements();

    return new HtmlSanitizer($config);
  }
}
