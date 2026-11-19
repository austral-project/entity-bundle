<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Tests\Fixtures;

use Austral\EntityBundle\Entity\Entity;

/**
 * Test entity with a unique string field, a plain string field and an integer field.
 *
 * @extends Entity<object>
 */
final class DuplicableEntity extends Entity
{
  /**
   * @var string
   */
  protected string $keyname = 'home';

  /**
   * @var string
   */
  protected string $title = 'Home';

  /**
   * @var int
   */
  protected int $position = 1;

  /**
   * @return string
   */
  public function getKeyname(): string
  {
    return $this->keyname;
  }

  /**
   * @param string $keyname
   *
   * @return $this
   */
  public function setKeyname(string $keyname): self
  {
    $this->keyname = $keyname;

    return $this;
  }

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   *
   * @return $this
   */
  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  /**
   * @return int
   */
  public function getPosition(): int
  {
    return $this->position;
  }

  /**
   * @param int $position
   *
   * @return $this
   */
  public function setPosition(int $position): self
  {
    $this->position = $position;

    return $this;
  }
}
