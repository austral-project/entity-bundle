<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityAttributes;

use Austral\EntityBundle\Attribute\AustralEntityAttributeInterface;
use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Austral EntityAttributes.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityAttributes
{
  /**
   * @var array<string, mixed>
   */
  protected array $fieldsAttributes = [];

  /**
   * @var array<string, AustralEntityAttributeInterface>
   */
  protected array $classAttributes = [];

  /**
   * @var string
   */
  protected string $slugger;

  /**
   * @var ClassMetadata<T>
   */
  protected ClassMetadata $classMetadata;

  /**
   * @param ClassMetadata<T> $classMetadata
   *
   * @return EntityAttributes<T>
   */
  public static function create(ClassMetadata $classMetadata): EntityAttributes
  {
    return new self($classMetadata);
  }

  /**
   * @param ClassMetadata<T> $classMetadata
   */
  public function __construct(ClassMetadata $classMetadata)
  {
    $this->classMetadata = $classMetadata;
    $this->slugger = AustralTools::slugger($this->classMetadata->getReflectionClass()->getShortName());
  }

  /**
   * @return class-string<T>
   */
  public function getClassname(): string
  {
    return $this->classMetadata->getName();
  }

  /**
   * @return string
   */
  public function getSlugger(): string
  {
    return $this->slugger;
  }

  /**
   * @param string $slugger
   *
   * @return $this
   */
  public function setSlugger(string $slugger): self
  {
    $this->slugger = $slugger;

    return $this;
  }

  /**
   * @param AustralEntityAttributeInterface $attribute
   *
   * @return $this
   */
  public function addFieldAttribute(AustralEntityAttributeInterface $attribute): self
  {
    $this->fieldsAttributes[$attribute->getKeyname()][$attribute->getClassname()] = $attribute;

    return $this;
  }

  /**
   * @param array<string, mixed> $attributes
   *
   * @return $this
   */
  public function addFieldsAttributes(array $attributes): self
  {
    $this->fieldsAttributes = array_merge($this->fieldsAttributes, $attributes);

    return $this;
  }

  /**
   * @return array<string, mixed>
   */
  public function getFieldsAttributes(): array
  {
    return $this->fieldsAttributes;
  }

  /**
   * @param string $keyname
   *
   * @return array<string, mixed>
   */
  public function getAttributesByKeyname(string $keyname): array
  {
    return array_key_exists($keyname, $this->fieldsAttributes) ? $this->fieldsAttributes[$keyname] : [];
  }

  /**
   * @param AustralEntityAttributeInterface $attribute
   *
   * @return $this
   */
  public function addClassAttribute(AustralEntityAttributeInterface $attribute): self
  {
    $this->classAttributes[$attribute->getClassname()] = $attribute;

    return $this;
  }

  /**
   * @param array<string, mixed> $attributes
   *
   * @return $this
   */
  public function addClassAttributes(array $attributes): self
  {
    $this->classAttributes = array_merge($this->classAttributes, $attributes);

    return $this;
  }

  /**
   * @return array<string, AustralEntityAttributeInterface>
   */
  public function getClassAttributes(): array
  {
    return $this->classAttributes;
  }
}
