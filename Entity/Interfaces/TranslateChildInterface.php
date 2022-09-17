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
 * Austral Entity Translate Child Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TranslateChildInterface
{

  /**
   * @return mixed
   */
  public function getId();

  /**
   * Set master
   *
   * @param TranslateMasterInterface $master
   *
   * @return $this
   */
  public function setMaster(TranslateMasterInterface $master): TranslateChildInterface;

  /**
   * Get master
   * @return TranslateMasterInterface
   */
  public function getMaster(): TranslateMasterInterface;

  /**
   * Get language
   *
   * @return string|null
   */
  public function getLanguage(): ?string;

  /**
   * Set language
   *
   * @param string|null $language
   * @return $this
   */
  public function setLanguage(?string $language): TranslateChildInterface;

  /**
   * Set isReferent
   *
   * @param bool $isReferent
   *
   * @return $this
   */
  public function setIsReferent(bool $isReferent): TranslateChildInterface;

  /**
   * Get isReferent
   * @return bool
   */
  public function getIsReferent(): bool;


}
