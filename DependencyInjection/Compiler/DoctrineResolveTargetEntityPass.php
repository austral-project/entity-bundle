<?php
/*
 * This file is part of the Austral ContentBlock Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Austral\EntityBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Version;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Austral Website Load Doctrine Resolve.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container)
  {
    $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
    if (version_compare(Version::VERSION, '2.5.0-DEV') < 0) {
      $definition->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
    } else {
      $definition->addTag('doctrine.event_subscriber', array('connection' => 'default', "priority"=>2048));
    }
  }
}