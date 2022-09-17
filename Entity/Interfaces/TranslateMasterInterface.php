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

use Doctrine\Common\Collections\Collection;

/**
 * Austral Entity Translate Master Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TranslateMasterInterface
{

  /**
   * @return mixed
   */
  public function getId();

  /**
   * @return TranslateChildInterface|mixed
   */
  public function createNewTranslateByLanguage();

  /**
   * @param string $name
   *
   * @return mixed
   */
  public function __get(string $name);

  /**
   * @param string $name
   * @param $value
   *
   * @return $this
   */
  public function __set(string $name, $value);

  /**
   * Add translates
   *
   * @param TranslateChildInterface $translate
   *
   * @return $this
   */
  public function addTranslates(TranslateChildInterface $translate): TranslateMasterInterface;

  /**
   * Remove translates
   *
   * @param TranslateChildInterface $translate
   *
   * @return $this
   */
  public function removeTranslates(TranslateChildInterface $translate): TranslateMasterInterface;

  /**
   * Get translates
   *
   * @return Collection
   */
  public function getTranslates(): Collection;

  /**
   * Remove All translates
   *
   * @return $this
   */
  public function removeAllTranslates(): TranslateMasterInterface;

  /**
   * @return array
   */
  public function getLanguages(): array;

  /**
   * @return string|null
   */
  public function getLanguageCurrent(): ?string;

  /**
   * @param string|null $value
   *
   * @return $this
   */
  public function setCurrentLanguage(?string $value): TranslateMasterInterface;

  /**
   * @param TranslateChildInterface $translate
   * @param $fieldKey
   *
   * @return mixed
   */
  public function getTranslateValueByKey(TranslateChildInterface $translate, $fieldKey);

  /**
   * @param TranslateChildInterface $translate
   * @param $fieldKey
   * @param null $value
   *
   * @return $this
   */
  public function setTranslateValueByKey(TranslateChildInterface $translate, $fieldKey, $value = null): TranslateMasterInterface;

  /**
   * @return array
   */
  public function getTranslatesByLanguage(): array;

  /**
   * @return TranslateChildInterface|null
   */
  public function getTranslateReferent(): ?TranslateChildInterface;

  /**
   * @param string $langue
   *
   * @return TranslateChildInterface|null
   */
  public function getTranslateByLanguage(string $langue): ?TranslateChildInterface;

  /**
   * @return TranslateChildInterface|null
   */
  public function getTranslateCurrent(): ?TranslateChildInterface;

  /**
   * @param TranslateChildInterface $child
   *
   * @return $this
   */
  public function addTranslateByLanguage(TranslateChildInterface $child): TranslateMasterInterface;

}
