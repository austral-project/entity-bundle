<?php
namespace Austral\EntityBundle\Sanitizer;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

interface AustralSanitizerInterface
{
  /**
   * Le nom de la configuration (ex: "default", "strict")
   */
  public function getName(): string;

  /**
   * L'instance du HtmlSanitizer configurée
   */
  public function getSanitizer(): HtmlSanitizerInterface;
}