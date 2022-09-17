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

use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function Symfony\Component\String\u;

/**
 * Austral Abstract Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
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
   * @param array $values
   * @return $this
   */
  public function hydrate(array $values = array()): EntityInterface
  {
    foreach($values as $key => $value)
    {
      if(property_exists($this, $key))
      {
        $this->$key = $value;
      }
    }
    return $this;
  }

  /**
   * @var string|int
   */
  protected $id;

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
   * @inheritDoc
   */
  public function __toString()
  {
    return $this->id;
  }

  /**
   * @inheritDoc
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @inheritDoc
   */
  public function setId(string $id): EntityInterface
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
   * @return EntityInterface
   */
  public function setIsCreate(bool $isCreate): EntityInterface
  {
    $this->isCreate = $isCreate;
    return $this;
  }

  /**
   * @return string[]
   */
  public function arrayObject(): array
  {
    return [
      "id"   =>  $this->getId()
    ];
  }

  /**
   * @inheritDoc
   */
  public function json(): string
  {
    return json_encode($this->arrayObject());
  }

  /**
   * @inheritDoc
   */
  public function serialize(): string
  {
    return serialize($this->arrayObject());
  }

  /**
   * @inheritDoc
   */
  public function getClassname(): string
  {
    if(!$this->classname)
    {
      $this->classname = (new ReflectionClass($this))->getShortName();
    }
    return $this->classname;
  }

  /**
   * @inheritDoc
   */
  public function getClassnameForMapping(): string
  {
    return $this->getEntityName();
  }

  /**
   * @inheritDoc
   */
  public function getEntityName(): string
  {
    if(!$this->entityName)
    {
      $this->entityName = ClassUtils::getRealClass((new ReflectionClass($this))->getName());
    }
    return $this->entityName;
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
   * @param string|null $value
   *
   * @return string|null
   */
  public function keynameGenerator(?string $value): ?string
  {
    return $value ? AustralTools::generatorKey($value) : "";
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
   * @throws \Exception
   */
  public function getValueByFieldname(string $fieldname)
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    if($propertyAccessor->isReadable($this, $fieldname))
    {
      return $propertyAccessor->getValue($this, $fieldname);
    }
    throw new \Exception("EntityFileBundle -> EntityFileTrait {$fieldname} not exist to Entity Class ".get_class($this));
  }

  /**
   * @param string $fieldname
   * @param mixed $value
   *
   * @return EntityInterface
   * @throws \Exception
   */
  public function setValueByFieldname(string $fieldname, $value = null)
  {
    $propertyAccessor = PropertyAccess::createPropertyAccessor();
    if($propertyAccessor->isWritable($this, $fieldname))
    {
      $propertyAccessor->setValue($this, $fieldname, $value);
    }
    return $this;
  }

  /**
   * @return bool
   */
  public function useAustralEntityTranslateBundle(): bool
  {
    return $this instanceof TranslateMasterInterface;
  }

}