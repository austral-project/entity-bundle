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

/**
 * Austral Entity Seo Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @deprecated
 */
interface SeoInterface
{

  /**
   * Get refTitle
   *
   * @return string|null
   */
  public function getRefTitle(): ?string;

  /**
   * Get refUrl
   *
   * @return string|null
   */
  public function getRefUrl(): ?string;

  /**
   * Get refUrlLast
   *
   * @return string|null
   */
  public function getRefUrlLast(): ?string;

  /**
   * Get refDescription
   *
   * @return string|null
   */
  public function getRefDescription(): ?string;

  /**
   * Get canonical
   *
   * @return string|null
   */
  public function getCanonical(): ?string;

  /**
   * @return string
   */
  public function getBaseUrl(): string;

}
