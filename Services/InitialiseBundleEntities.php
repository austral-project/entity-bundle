<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Austral Initialise Bundle Entities service.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class InitialiseBundleEntities
{

  /**
   * @var ContainerInterface
   */
  protected ContainerInterface $container;

  /**
   * @var Filesystem
   */
  protected Filesystem $filesystem;

  /**
   * InitialiseBundleEntities constructor.
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
    $this->filesystem = new Filesystem();
  }

  /**
   * @return $this
   * @throws \ReflectionException
   */
  public function init(): InitialiseBundleEntities
  {
    $bundles = $this->container->getParameter("kernel.bundles");
    $rootPathEntities = $this->container->getParameter("austral.entity.initialise-bundle.path");
    $skeletonDirs = array();
    foreach($bundles as $bundleName => $bundle)
    {
      if(preg_match('/Austral(.*Bundle)/iuU', $bundleName, $matches))
      {
        $reflectionClass = new \ReflectionClass($bundle);
        $bundleDir = dirname($reflectionClass->getFileName());
        $skeletonDir = "{$bundleDir}/Skeleton/Entity";
        if(file_exists($skeletonDir) && is_dir($skeletonDir))
        {
          $skeletonDirs[$matches[1]] = $skeletonDir;
        }
      }
    }

    if(count($skeletonDirs))
    {
      if(!file_exists($rootPathEntities))
      {
        $this->filesystem->mkdir($rootPathEntities);
      }
      foreach($skeletonDirs as $bundleName => $skeletonDir)
      {
        $entitiesPath = "{$rootPathEntities}/{$bundleName}";
        if(!file_exists($entitiesPath))
        {
          $this->filesystem->mkdir($entitiesPath);
        }
        foreach (scandir($skeletonDir) as $file)
        {
          if(!in_array($file, array('.', "..")))
          {
            if(!file_exists("{$entitiesPath}/{$file}"))
            {
              $fileContent = file_get_contents("{$skeletonDir}/{$file}");
              $fileContent = str_replace("##php##", "<?php", $fileContent);
              file_put_contents("{$entitiesPath}/{$file}", $fileContent);
            }
          }
        }
      }
    }
    return $this;
  }

}