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
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;

/**
 * Austral Entity Filter by Domain Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface FilterByDomainInterface
{

  /**
   * @return string|null
   */
  public function getDomainId(): ?string;

  /**
   * @param string|null $domainId
   *
   * @return FilterByDomainInterface
   */
  public function setDomainId(?string $domainId = null): FilterByDomainInterface;

  /**
   * @return DomainInterface|EntityInterface|null
   */
  public function getDomain(): ?EntityInterface;

  /**
   * @param DomainInterface|EntityInterface $domain
   *
   * @return FilterByDomainInterface|null
   */
  public function setDomain(EntityInterface $domain): ?FilterByDomainInterface;

}

    
    
      