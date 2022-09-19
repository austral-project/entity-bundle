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

/**
 * Austral Interface Entity.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityInterface
{

  /**
   * @return string
   */
  public function __toString();

  /**
   * @return string|int
   */
  public function getId();

  /**
   * @param string $id
   *
   * @return self
   */
  public function setId(string $id): EntityInterface;

  /**
   * @return bool
   */
  public function getIsCreate(): bool;

  /**
   * @param bool $isCreate
   *
   * @return EntityInterface
   */
  public function setIsCreate(bool $isCreate): EntityInterface;

  /**
   * @return array
   */
  public function arrayObject(): array;

  /**
   * @return string
   */
  public function json(): string;

  /**
   * @return string
   */
  public function serialize(): string;

  /**
   * @return string
   */
  public function getClassname(): string;

  /**
   * @return string
   */
  public function getClassnameForMapping(): string;

  /**
   * @return string
   */
  public function getSnakeClassname(): string;

  /**
   * @return string
   */
  public function getSluggerClassname(): string;

  /**
   * @return string
   */
  public function getEntityName(): string;

  /**
   * @param array $values
   *
   * @return self
   */
  public function hydrate(array $values = array()): EntityInterface;

  /**
   * @param string $fieldname
   *
   * @return mixed
   * @throws \Exception
   */
  public function getValueByFieldname(string $fieldname);

  /**
   * @param string $fieldname
   * @param mixed $value
   *
   * @return EntityInterface
   * @throws \Exception
   */
  public function setValueByFieldname(string $fieldname, $value = null);


}
