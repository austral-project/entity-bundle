<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Mapping;

use Austral\EntityBundle\Entity\EntityInterface;
use Closure;
use Exception;

/**
 * Austral FieldMappingInterface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @implements
 */
interface FieldMappingInterface
{

  /**
   * @param EntityMapping $entityMapping
   *
   * @return $this
   */
  public function setEntityMapping(EntityMapping $entityMapping): FieldMappingInterface;

  /**
   * @return string
   */
  public function getClassname(): string;

  /**
   * @return string|null
   */
  public function getFieldname(): ?string;

  /**
   * @param string $fieldname
   *
   * @return FieldMappingInterface
   */
  public function setFieldname(string $fieldname): FieldMappingInterface;

  /**
   * @param Closure $getterObjectValueFonction
   *
   * @return $this
   */
  public function addGetterObjectValueFonction(Closure $getterObjectValueFonction): FieldMappingInterface;

  /**
   * @param Closure $setterObjectValueFonction
   *
   * @return $this
   */
  public function addSetterObjectValueFonction(Closure $setterObjectValueFonction): FieldMappingInterface;

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   *
   * @return mixed
   * @throws Exception
   */
  public function getObjectValue(EntityInterface $object, string $fieldname = null);

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   * @param null $value
   *
   * @return $this
   * @throws Exception
   */
  public function setObjectValue(EntityInterface $object, string $fieldname = null, $value = null): FieldMappingInterface;


}