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

class AustralHtmlSanitizer implements AustralSanitizerInterface
{
  public function getName(): string
  {
    return 'html';
  }

  public function getSanitizer(): HtmlSanitizerInterface
  {
    $config = (new HtmlSanitizerConfig())
      // Line breaks & paragraphs
      ->allowElement('p')
      ->allowElement('br')

      // Inline formatting
      ->allowElement('strong')
      ->allowElement('b')
      ->allowElement('em')
      ->allowElement('i')
      ->allowElement('u')
      ->allowElement('s')

      // Headings
      ->allowElement('h1')
      ->allowElement('h2')
      ->allowElement('h3')
      ->allowElement('h4')
      ->allowElement('h5')
      ->allowElement('h6')

      // Lists
      ->allowElement('ul')
      ->allowElement('ol')
      ->allowElement('li')

      // Quotes
      ->allowElement('blockquote')
      ->allowElement('q')

      // Containers
      ->allowElement('div', ['class'])
      ->allowElement('span', ['class'])

      // Links
      ->allowElement('a', ['href', 'title', 'target', 'rel'])
      ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
      ->allowRelativeLinks(true)

      // Images
      ->allowElement('img', ['src', 'alt', 'width', 'height', 'loading']);

    return new HtmlSanitizer($config);
  }
}
