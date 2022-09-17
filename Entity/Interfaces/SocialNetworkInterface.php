<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Entity\Interfaces;

use Austral\EntityBundle\Entity\EntityInterface;

/**
 * Austral Entity Social Network Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @deprecated
 */
interface SocialNetworkInterface
{

  /**
   * Set socialTitle
   *
   * @param string|null $socialTitle
   *
   * @return SocialNetworkInterface|EntityInterface
   */
  public function setSocialTitle(?string $socialTitle): SocialNetworkInterface;

  /**
   * Get socialTitle
   *
   * @return string|null
   */
  public function getSocialTitle(): ?string;

  /**
   * Set socialDescription
   *
   * @param string|null $socialDescription
   *
   * @return SocialNetworkInterface|EntityInterface
   */
  public function setSocialDescription(?string $socialDescription): SocialNetworkInterface;

  /**
   * Get socialDescription
   *
   * @return string|null
   */
  public function getSocialDescription(): ?string;

  /**
   * Set socialImage
   *
   * @param string|null $socialImage
   *
   * @return SocialNetworkInterface|EntityInterface
   */
  public function setSocialImage(?string $socialImage): SocialNetworkInterface;

  /**
   * Get socialImage
   *
   * @return string|null
   */
  public function getSocialImage(): ?string;

}
