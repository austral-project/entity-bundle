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
use Exception;

/**
 * Austral FieldMappingInterface.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface FieldMappingInterface
{

  /**
   * @param EntityMapping<T> $entityMapping
   *
   * @return $this
   */
  public function setEntityMapping(EntityMapping $entityMapping): self;

  /**
   * @return class-string
   */
  public function getClassname(): string;

  /**
   * @return string|null
   */
  public function getFieldname(): ?string;

  /**
   * @param string $fieldname
   *
   * @return $this
   */
  public function setFieldname(string $fieldname): self;

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
   * @param string|null $fieldname
   *
   * @return mixed
   * @throws Exception
   */
  public function getObjectValue(EntityInterface $object, ?string $fieldname = null): mixed;

  /**
   * @param EntityInterface<T> $object
   * @param string|null $fieldname
   * @param mixed $value
   *
   * @return $this
   * @throws Exception
   */
  public function setObjectValue(EntityInterface $object, ?string $fieldname = null, mixed $value = null): self;


}