<?php
/*
 * This file is part of the Austral Seo Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Listener;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\EntityBundle\Services\Sanitizer;
use Austral\SeoBundle\Entity\Interfaces\UrlParameterInterface;
use Austral\SeoBundle\Entity\Redirection;
use Austral\SeoBundle\Entity\Traits\UrlParameterTrait;
use Austral\SeoBundle\Entity\UrlParameter;
use Austral\SeoBundle\Mapping\UrlParameterMapping;
use Austral\SeoBundle\Services\RedirectionManagement;
use Austral\SeoBundle\Services\UrlParameterManagement;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Austral Doctrine Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
#[AsDoctrineListener(event: 'preUpdate', connection: 'default')]
#[AsDoctrineListener(event: 'prePersist', connection: 'default')]
class DoctrineListener implements EventSubscriber
{

  /**
   * @var mixed
   */
  protected $name;

  /**
   * @var bool
   */
  protected bool $postFlush = false;

  /**
   * DoctrineListener constructor.
   */
  public function __construct(protected Sanitizer $sanitizer)
  {
    $parts = explode('\\', $this->getNamespace());
    $this->name = end($parts);
  }

  /**
   * @return array
   */
  public function getSubscribedEvents()
  {
    return array(
      'preUpdate',
      "prePersist"
    );
  }

  /**
   * @param PrePersistEventArgs $args
   *
   * @throws \Exception
   */
  public function prePersist(PrePersistEventArgs $args)
  {
    /** @var EntityInterface $object */
    $object = $args->getObject();
    $this->sanitizer->sanitize($object);
  }

  /**
   * @param PreUpdateEventArgs $args
   *
   * @throws \Exception
   */
  public function preUpdate(PreUpdateEventArgs $args)
  {
    /** @var EntityInterface $object */
    $object = $args->getObject();
    $this->sanitizer->sanitize($object);
  }

  /**
   * Get an event adapter to handle event specific
   * methods
   *
   * @param EventArgs $args
   *
   * @return EventArgs
   */
  protected function getEventAdapter(EventArgs $args): EventArgs
  {
    return $args;
  }

  /**
   * @return string
   */
  protected function getNamespace(): string
  {
    return __NAMESPACE__;
  }

}