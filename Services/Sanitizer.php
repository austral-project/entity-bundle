<?php
/*
 * This file is part of the Austral EntityFile Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Services;

use Austral\EntityBundle\Annotation\AustralEntitySanitize;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class Sanitizer
{
  /** @var array<string, HtmlSanitizerInterface> */
  private array $sanitizers = [];

  public function __construct(
    #[Autowire(service: "annotation_reader")] private readonly Reader $annotationReader,
    #[TaggedIterator('austral_entity.sanitizer_rule')] iterable $sanitizers = array()
  ) {
    foreach ($sanitizers as $sanitizer) {
      $this->sanitizers[$sanitizer->getName()] = $sanitizer->getSanitizer();
    }
  }

  public function sanitize(object $entity): void
  {
    $reflectionClass = new \ReflectionClass($entity);

    foreach ($reflectionClass->getProperties() as $property) {
      $annotations = $this->annotationReader->getPropertyAnnotations($property);

      foreach ($annotations as $annotation) {
        if (!$annotation instanceof AustralEntitySanitize) {
          continue;
        }

        $property->setAccessible(true);
        $currentValue = $property->getValue($entity);

        if (!is_string($currentValue) || empty($currentValue)) {
          continue;
        }

        if (isset($this->sanitizers[$annotation->config])) {
          $sanitizer = $this->sanitizers[$annotation->config];
          $sanitizedValue = $sanitizer->sanitize($currentValue);
          $property->setValue($entity, $sanitizedValue);
        } else {
          throw new \InvalidArgumentException(
            sprintf('La configuration de sanitization "%s" n\'existe pas.', $annotation->config)
          );
        }
      }
    }
  }
}