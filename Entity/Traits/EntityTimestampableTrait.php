<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\EntityBundle\Entity\Traits;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Austral Entity TimestampableTrait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
trait EntityTimestampableTrait
{

  /**
   * @var DateTime|null
   */
  #[Gedmo\Timestampable(on: "create")]
  #[ORM\Column(name: "created", type: "datetime", nullable: true)]
  protected ?DateTime $created = null;

  /**
   * @var DateTime|null
   */
  #[Gedmo\Timestampable(on: "update")]
  #[ORM\Column(name: "updated", type: "datetime", nullable: true)]
  protected ?DateTime $updated = null;

  /**
   * Set created
   *
   * @param DateTime $created
   *
   * @return $this
   */
  public function setCreated(DateTime $created): static
  {
    $this->created = $created;
    return $this;
  }

  /**
   * Get created
   *
   * @return DateTime
   */
  public function getCreated(): DateTime
  {
    return $this->created ?: new DateTime();
  }

  /**
   * Set updated
   *
   * @param DateTime $updated
   *
   * @return $this
   */
  public function setUpdated(DateTime $updated): static
  {
    $this->updated = $updated;
    return $this;
  }

  /**
   * Get updated
   *
   * @return DateTime
   */
  public function getUpdated(): DateTime
  {
    return $this->updated ?: new DateTime();
  }
  
}
