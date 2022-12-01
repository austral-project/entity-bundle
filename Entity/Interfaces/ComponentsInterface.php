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
use Austral\ContentBlockBundle\Entity\Interfaces\ComponentInterface;
use DateTime;

/**
 * Austral Entity Components Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface ComponentsInterface
{

  /**
   * @return array
   */
  public function getComponents(): array;

  /**
   * @param string $containerName
   *
   * @return array
   */
  public function getComponentsByContainerName(string $containerName): array;

  /**
   * @return array
   */
  public function getComponentsRemoved(): array;

  /**
   * @return array
   */
  public function getComponentsContainerNames(): array;

  /**
   * @return array
   */
  public function getComponentsTemplate(): array;

  /**
   * @param string $containerName
   *
   * @return array
   */
  public function getComponentsTemplateByContainerName(string $containerName = "master"): array;

  /**
   * @param array $components
   * @param bool $updated
   *
   * @return $this
   */
  public function setComponents(array $components, bool $updated = true): ComponentsInterface;

  /**
   * @param array $componentsTemplate
   *
   * @return $this|ComponentsInterface
   */
  public function setComponentsTemplate(array $componentsTemplate): ComponentsInterface;

  /**
   * Add child
   *
   * @param string $containerName
   * @param ComponentInterface $child
   *
   * @return $this
   */
  public function addComponents(string $containerName, ComponentInterface $child): ComponentsInterface;

  /**
   * Remove child
   *
   * @param string $containerName
   * @param ComponentInterface $child
   *
   * @return $this
   */
  public function removeComponents(string $containerName, ComponentInterface $child): ComponentsInterface;

  /**
   * @return ?DateTime
   */
  public function getComponentsUpdated(): ?DateTime;

  /**
   * @param DateTime $componentsUpdated
   *
   * @return ComponentsInterface
   */
  public function setComponentsUpdated(DateTime $componentsUpdated): ComponentsInterface;



}
