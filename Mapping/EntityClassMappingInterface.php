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

use Austral\Contracts\Entity\EntityInterface;

/**
 * Austral EntityClassMappingInterface.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityClassMappingInterface
{

  /**
   * @param EntityMapping<T> $entityMapping
   *
   * @return $this
   */
  public function setEntityMapping(EntityMapping $entityMapping): EntityClassMappingInterface;

  /**
   * @return class-string
   */
  public function getClassname(): string;

  /**
   * @param (\Closure(EntityInterface<T>, string): mixed) $getterObjectValueFonction
   *
   * @return $this
   */
  public function addGetterObjectValueFonction(\Closure $getterObjectValueFonction): self;

  /**
   * @param (\Closure(EntityInterface<T>, string, mixed): mixed) $setterObjectValueFonction
   *
   * @return $this
   */
  public function addSetterObjectValueFonction(\Closure $setterObjectValueFonction): self;

  /**
   * @param EntityInterface<T> $object
   * @param string $fieldname
   *
   * @return mixed
   * @throws \Exception
   */
  public function getObjectValue(EntityInterface $object, string $fieldname): mixed;

  /**
   * @param EntityInterface<T> $object
   * @param string $fieldname
   * @param null $value
   *
   * @return $this
   * @throws \Exception
   */
  public function setObjectValue(EntityInterface $object, string $fieldname, $value = null): self;


}