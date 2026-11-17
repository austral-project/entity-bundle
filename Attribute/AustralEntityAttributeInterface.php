<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\EntityBundle\Attribute;

/**
 * @Attribute
 */
interface AustralEntityAttributeInterface
{

  /**
   * @param string $keyname
   *
   * @return $this
   */
  public function setKeyname(string $keyname): AustralEntityAttributeInterface;

  /**
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * @return string
   */
  public function getClassname(): string;

  /**
   * @return string
   */
  public function getClassnameShort(): string;

}