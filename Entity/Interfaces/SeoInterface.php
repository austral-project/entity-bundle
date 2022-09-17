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
   * Get refH1
   *
   * @return SeoInterface|null
   */
  public function getPageParent(): ?SeoInterface;

  /**
   * Set refH1
   *
   * @param string|null $refH1
   *
   * @return $this
   */
  public function setRefH1(?string $refH1): SeoInterface;

  /**
   * Get refH1
   *
   * @return string|null
   */
  public function getRefH1(): ?string;

  /**
   * @return string|null
   */
  public function getRefH1OrDefault(): ?string;

  /**
   * Set refTitle
   *
   * @param string|null $refTitle
   *
   * @return $this
   */
  public function setRefTitle(?string $refTitle): SeoInterface;

  /**
   * Get refTitle
   *
   * @return string|null
   */
  public function getRefTitle(): ?string;

  /**
   * Set refUrl
   *
   * @param string|null $refUrl
   *
   * @return $this
   */
  public function setRefUrl(?string $refUrl): SeoInterface;

  /**
   * Get refUrl
   *
   * @return string|null
   */
  public function getRefUrl(): ?string;

  /**
   * Set refUrlLast
   *
   * @param string|null $refUrlLast
   *
   * @return $this
   */
  public function setRefUrlLast(?string $refUrlLast): SeoInterface;

  /**
   * Get refUrlLast
   *
   * @return string|null
   */
  public function getRefUrlLast(): ?string;

  /**
   * @return bool
   */
  public function getRefUrlLastEnabled(): bool;

  /**
   * Set refDescription
   *
   * @param string|null $refDescription
   *
   * @return $this
   */
  public function setRefDescription(?string $refDescription): SeoInterface;

  /**
   * Get refDescription
   *
   * @return string|null
   */
  public function getRefDescription(): ?string;

  /**
   * Set canonical
   *
   * @param string|null $canonical
   *
   * @return $this
   */
  public function setCanonical(?string $canonical): SeoInterface;

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
