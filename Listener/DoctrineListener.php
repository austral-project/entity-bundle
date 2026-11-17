<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Listener;

use Austral\EntityBundle\Services\Sanitizer;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Sanitizes the #[AustralEntitySanitize] properties before insert and update.
 * Declared in Resources/config/services.yaml (prePersist and preUpdate events).
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener
{

  /**
   * @param Sanitizer $sanitizer
   */
  public function __construct(protected Sanitizer $sanitizer)
  {
  }

  /**
   * @param PrePersistEventArgs $args
   *
   * @return void
   */
  public function prePersist(PrePersistEventArgs $args): void
  {
    $this->sanitizer->sanitize($args->getObject());
  }

  /**
   * In preUpdate, Doctrine has already computed the changes: updating the entity is not enough,
   * the sanitized value must be set in the changeset.
   *
   * @param PreUpdateEventArgs $args
   *
   * @return void
   */
  public function preUpdate(PreUpdateEventArgs $args): void
  {
    foreach($this->sanitizer->sanitize($args->getObject()) as $fieldname => $value)
    {
      if($args->hasChangedField($fieldname))
      {
        $args->setNewValue($fieldname, $value);
      }
    }
  }

}
