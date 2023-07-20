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

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
final class Slugger
{

  /**
   * @var string|null
   */
  public ?string $slugger = null;

  /**
   * @param string|null $slugger
   */
  public function __construct(?string $slugger = null)
  {
    $this->slugger = $slugger;
  }

}
