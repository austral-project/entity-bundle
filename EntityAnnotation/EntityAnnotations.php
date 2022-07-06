<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityAnnotation;

use Austral\EntityBundle\Annotation\AustralEntityAnnotationInterface;
use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Austral EntityAnnotation.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityAnnotations
{

  /**
   * @var array
   */
  protected array $fieldsAnnotations = array();

  /**
   * @var array
   */
  protected array $classAnnotations = array();

  /**
   * @var string
   */
  protected string $slugger;

  /**
   * @var ClassMetadata
   */
  protected ClassMetadata $classMetadata;

  /**
   * @param ClassMetadata $classMetadata
   *
   * @return EntityAnnotations
   */
  public static function create(ClassMetadata $classMetadata): EntityAnnotations
  {
    return new self($classMetadata);
  }

  /**
   * @param ClassMetadata $classMetadata
   */
  public function __construct(ClassMetadata $classMetadata)
  {
    $this->classMetadata = $classMetadata;
    $this->slugger = AustralTools::slugger($this->classMetadata->getReflectionClass()->getShortName());
  }

  /**
   * @return string
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
   * @param AustralEntityAnnotationInterface $annotation
   *
   * @return $this
   */
  public function addFieldAnnotation(AustralEntityAnnotationInterface $annotation): EntityAnnotations
  {
    $this->fieldsAnnotations[$annotation->getKeyname()][$annotation->getClassname()] = $annotation;
    return $this;
  }

  /**
   * @param array $annotations
   *
   * @return $this
   */
  public function addFieldsAnnotations(array $annotations): EntityAnnotations
  {
    $this->fieldsAnnotations = array_merge($this->fieldsAnnotations, $annotations);
    return $this;
  }

  /**
   * @return array
   */
  public function getFieldsAnnotations(): array
  {
    return $this->fieldsAnnotations;
  }

  /**
   * @param string $keyname
   *
   * @return array
   */
  public function getAnnotationsByKeyname(string $keyname): array
  {
    return array_key_exists($keyname, $this->fieldsAnnotations) ? $this->fieldsAnnotations[$keyname] : array();
  }

  /**
   * @param AustralEntityAnnotationInterface $annotation
   *
   * @return $this
   */
  public function addClassAnnotation(AustralEntityAnnotationInterface $annotation): EntityAnnotations
  {
    $this->classAnnotations[$annotation->getClassname()] = $annotation;
    return $this;
  }

  /**
   * @param array $annotations
   *
   * @return $this
   */
  public function addClassAnnotations(array $annotations): EntityAnnotations
  {
    $this->classAnnotations = array_merge($this->classAnnotations, $annotations);
    return $this;
  }

  /**
   * @return array
   */
  public function getClassAnnotations(): array
  {
    return $this->classAnnotations;
  }



}
