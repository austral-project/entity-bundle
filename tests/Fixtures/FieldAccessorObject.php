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

/**
 * Object exposing every kind of access FieldAccessor has to recognise, plus magic methods it must ignore.
 */
final class FieldAccessorObject
{
  /**
   * @var string
   */
  public string $publicField = 'public';

  /**
   * @var string
   */
  private string $title = 'Home';

  /**
   * @var string
   */
  private string $hidden = 'hidden';

  /**
   * @var bool
   */
  private bool $enabled = true;

  /**
   * @var list<string>
   */
  private array $tags = [];

  /**
   * @var array<string, mixed>
   */
  private array $magic = [];

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Reads $hidden without being an accessor for it: "hidden" must stay unreadable.
   */
  public function describe(): string
  {
    return $this->title.' ('.$this->hidden.')';
  }

  public function isEnabled(): bool
  {
    return $this->enabled;
  }

  public function hasChildren(): bool
  {
    return false;
  }

  public function label(): string
  {
    return 'label';
  }

  /**
   * @return list<string>
   */
  public function getTags(): array
  {
    return $this->tags;
  }

  public function addTag(string $tag): self
  {
    $this->tags[] = $tag;

    return $this;
  }

  public function removeTag(string $tag): self
  {
    $this->tags = array_values(array_filter($this->tags, static fn (string $value): bool => $value !== $tag));

    return $this;
  }

  public function __get(string $name): mixed
  {
    return $this->magic[$name] ?? 'magic';
  }

  public function __set(string $name, mixed $value): void
  {
    $this->magic[$name] = $value;
  }

  public function __isset(string $name): bool
  {
    return true;
  }
}
