<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Command;

use Austral\ToolsBundle\Command\Base\Command;
use Austral\ToolsBundle\Command\Exception\CommandException;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Austral Initialise Bundle Entities Command.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class InitialiseBundleEntitiesCommand extends Command
{

  /**
   * @var string
   */
  protected static $defaultName = 'austral:entity:initialise-bundle';

  /**
   * @var bool
   */
  protected bool $initEntityMapping = false;

  /**
   * {@inheritdoc}
   */
  protected function configure()
  {
    $this
      ->setDefinition([
      ])
      ->setDescription($this->titleCommande)
      ->setHelp(<<<'EOF'
The <info>%command.name%</info> command to initialise bundle entities

  <info>php %command.full_name%</info>
EOF
      )
    ;
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   *
   * @throws CommandException
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  protected function executeCommand(InputInterface $input, OutputInterface $output)
  {
    $this->container->get("austral.entity.initialise-bundle")->init();
  }


}