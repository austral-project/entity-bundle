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
 * Austral EntityClassMappingInterface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @implements
 */
interface EntityClassMappingInterface
{

  /**
   * @param EntityMapping $entityMapping
   *
   * @return $this
   */
  public function setEntityMapping(EntityMapping $entityMapping): EntityClassMappingInterface;

  /**
   * @return string
   */
  public function getClassname(): string;

  /**
   * @param Closure $getterObjectValueFonction
   *
   * @return $this
   */
  public function addGetterObjectValueFonction(Closure $getterObjectValueFonction): EntityClassMappingInterface;

  /**
   * @param Closure $setterObjectValueFonction
   *
   * @return $this
   */
  public function addSetterObjectValueFonction(Closure $setterObjectValueFonction): EntityClassMappingInterface;

  /**
   * @param EntityInterface $object
   * @param string $fieldname
   *
   * @return mixed
   * @throws Exception
   */
  public function getObjectValue(EntityInterface $object, string $fieldname);

  /**
   * @param EntityInterface $object
   * @param string $fieldname
   * @param null $value
   *
   * @return $this
   * @throws Exception
   */
  public function setObjectValue(EntityInterface $object, string $fieldname, $value = null): EntityClassMappingInterface;


}