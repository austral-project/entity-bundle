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
 * Austral Entity TreePage Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface TreePageInterface
{

  /**
   * Get PageParent
   *
   * @return TreePageInterface|EntityInterface|null
   */
  public function getTreePageParent(): ?TreePageInterface;

  /**
   * Get refH1
   *
   * @param TreePageInterface $parent
   *
   * @return TreePageInterface|null
   */
  public function setTreePageParent(TreePageInterface $parent): ?TreePageInterface;


}
