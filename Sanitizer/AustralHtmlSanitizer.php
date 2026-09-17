<?php
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
      // Sauts de ligne & paragraphes
      ->allowElement('p')
      ->allowElement('br')

      // Formatage inline
      ->allowElement('strong')
      ->allowElement('b')
      ->allowElement('em')
      ->allowElement('i')
      ->allowElement('u')
      ->allowElement('s')

      // Titres
      ->allowElement('h1')
      ->allowElement('h2')
      ->allowElement('h3')
      ->allowElement('h4')
      ->allowElement('h5')
      ->allowElement('h6')

      // Listes
      ->allowElement('ul')
      ->allowElement('ol')
      ->allowElement('li')

      // Citations
      ->allowElement('blockquote')
      ->allowElement('q')

      // Conteneurs
      ->allowElement('div', ['class'])
      ->allowElement('span', ['class'])

      // Liens
      ->allowElement('a', ['href', 'title', 'target', 'rel'])
      ->allowLinkSchemes(['http', 'https', 'mailto', 'tel'])
      ->allowRelativeLinks(true)

      // Images
      ->allowElement('img', ['src', 'alt', 'width', 'height', 'loading']);

    return new HtmlSanitizer($config);
  }
}