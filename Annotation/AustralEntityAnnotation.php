<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\EntityBundle\Annotation;

/**
 * @Annotation
 * @abstract
 */
abstract class AustralEntityAnnotation implements AustralEntityAnnotationInterface
{
  /**
   * @var string|null
   */
  public ?string $keyname = null;

  /**
   * @return string|null
   */
  public function getKeyname(): ?string
  {
    return $this->keyname;
  }

  /**
   * @param string $keyname
   *
   * @return $this
   */
  public function setKeyname(string $keyname): AustralEntityAnnotationInterface
  {
    $this->keyname = $keyname;
    return $this;
  }

  /**
   * @return string
   */
  public function getClassname(): string
  {
    return get_class($this);
  }

  /**
   * @return string
   */
  public function getClassnameShort(): string
  {
    return (new \ReflectionClass($this))->getShortName();
  }

}