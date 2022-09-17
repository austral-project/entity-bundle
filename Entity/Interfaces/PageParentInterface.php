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
 * Austral Entity Page Parent Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface PageParentInterface
{
  /**
   * Get refH1
   *
   * @return SeoInterface|PageParentInterface|null
   */
  public function getPageParent(): ?SeoInterface;

  /**
   * @param SeoInterface $page
   *
   * @return $this
   */
  public function setPageParent(SeoInterface $page): SeoInterface;
}

    
    
      