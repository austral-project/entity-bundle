<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\EntityManager;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;

/**
 * Austral Interface EntityManagerORM.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityManagerORMInterface extends EntityManagerInterface
{

  /**
   * @return array
   */
  public function getFieldsMappingAll(): array;

  /**
   * @return array
   */
  public function getFieldsMappingWithAssociation(): array;

  /**
   * @param null $class
   *
   * @return array
   */
  public function getFieldsMapping($class = null): array;

  /**
   * @return DoctrineEntityManagerInterface
   */
  public function getDoctrineEntityManager(): DoctrineEntityManagerInterface;



}
