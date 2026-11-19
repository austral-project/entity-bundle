<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Entity;

use Austral\Contracts\Entity\EntityInterface;
use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyAccess;

use function Symfony\Component\String\u;

/**
 * Austral Abstract Entity.
 *
 * @template T of object
 *
 * @implements EntityInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 *
 * @ORM\MappedSuperclass
 */
abstract class Entity implements EntityInterface
{
  /**
   * Construct
   */
  public function __construct()
  {
    $this->isCreate = true;
  }

  /**
   * Destruct
   */
  public function __destruct()
  {
  }

  /**
   * @param array<string, mixed> $values
   *
   * @return $this
   */
  public function hydrate(array $values = []): EntityInterface
  {
    foreach ($values as $key => $value)
    {
      if (property_exists($this, $key))
      {
        $this->$key = $value;
      }
    }

    return $this;
  }

  /**
   * @var string
   */
  protected string $id = 'create';

  /**
   * @var bool
   */
  protected bool $isCreate = false;

  /**
   * @var string|null
   */
  protected ?string $classname = null;

  /**
   * @var string|null
   */
  protected ?string $entityName = null;

  /**
   * {@inheritDoc}
   */
  public function __toString()
  {
    return $this->id;
  }

  /**
   * {@inheritDoc}
   */
  public function getId(): string
  {
    return $this->id;
  }

  /**
   * {@inheritDoc}
   */
  public function setId(string $id): self
  {
    $this->id = $id;

    return $this;
  }

  /**
   * @return bool
   */
  public function getIsCreate(): bool
  {
    return $this->isCreate;
  }

  /**
   * @param bool $isCreate
   *
   * @return $this
   */
  public function setIsCreate(bool $isCreate): self
  {
    $this->isCreate = $isCreate;

    return $this;
  }

  /**
   * @return array<string, mixed>
   */
  public function arrayObject(): array
  {
    return [
      'id' => $this->getId(),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function json(): string
  {
    $json = json_encode($this->arrayObject());

    return $json ?: '{}';
  }

  /**
   * {@inheritDoc}
   *
   * @deprecated since 4.0, use json() instead
   */
  public function serialize(): string
  {
    return $this->json();
  }

  /**
   * {@inheritDoc}
   */
  public function getClassname(): string
  {
    if (!$this->classname)
    {
      $this->classname = new \ReflectionClass($this)->getShortName();
    }

    return $this->classname;
  }

  /**
   * {@inheritDoc}
   */
  public function getClassnameForMapping(): string
  {
    return $this->getEntityName();
  }

  /**
   * {@inheritDoc}
   */
  public function getEntityName(): string
  {
    if (!$this->entityName)
    {
      $this->entityName = new \ReflectionClass($this)->getName();
    }

    return $this->entityName;
  }

  /**
   * objectKey
   *
   * @return string
   */
  public function objectKey(): string
  {
    return "{$this->getEntityName()}:{$this->getId()}";
  }

  /**
   * @return string
   */
  public function getSnakeClassname(): string
  {
    return u($this->getClassname())->snake()->toString();
  }

  /**
   * @return string
   */
  public function getSluggerClassname(): string
  {
    return $this->keynameGenerator($this->getClassname());
  }

  /**
   * @param string $value
   *
   * @return string
   */
  public function keynameGenerator(string $value): string
  {
    return $value ? AustralTools::generatorKey($value) : '';
  }

  /**
   * @return string|null
   */
  public function getKeyname(): ?string
  {
    return "{$this->getSnakeClassname()}-{$this->getId()}";
  }

  /**
   * @param string $fieldname
   *
   * @return mixed
   *
   * @throws \Exception
   */
  public function getValueByFieldname(string $fieldname): mixed
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    if ($propertyAccessor->isReadable($this, $fieldname))
    {
      return $propertyAccessor->getValue($this, $fieldname);
    }

    return null;
  }

  /**
   * {@inheritDoc}
   */
  public function setValueByFieldname(string $fieldname, mixed $value = null): self
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    if ($propertyAccessor->isWritable($this, $fieldname))
    {
      $propertyAccessor->setValue($this, $fieldname, $value);
    }

    return $this;
  }
}
