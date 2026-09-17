<?php
/*
 * This file is part of the Austral EntityFile Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\EntityBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"PROPERTY", "ANNOTATION"})
 */
final class AustralEntitySanitize extends AustralEntityAnnotation
{
  /**
   * @var string|null
   */
  public ?string $config = "";

  /**
   * @param string|null $config
   */
  public function __construct(?string $config = null)
  {
    $this->config = $config ?? "default";
  }
}