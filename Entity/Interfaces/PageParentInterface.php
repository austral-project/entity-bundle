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
 * Austral Entity Page Parent Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface PageParentInterface
{
  /**
   * Get refH1
   *
   * @return TreePageInterface|PageParentInterface|EntityInterface|null
   */
  public function getTreePageParent(): ?TreePageInterface;

  /**
   * @param TreePageInterface $page
   *
   * @return TreePageInterface
   */
  public function setTreePageParent(TreePageInterface $parent): ?TreePageInterface;
}

    
    
      