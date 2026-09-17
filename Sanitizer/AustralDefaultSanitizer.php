<?php
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
    $config = (new HtmlSanitizerConfig())->allowSafeElements();
    return new HtmlSanitizer($config);
  }
}