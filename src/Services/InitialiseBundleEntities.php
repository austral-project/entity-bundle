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

use Symfony\Component\Filesystem\Filesystem;

/**
 * Austral Initialise Bundle Entities service.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @final
 */
class InitialiseBundleEntities
{
  /**
   * @var Filesystem
   */
  protected Filesystem $filesystem;

  /**
   * InitialiseBundleEntities constructor
   *
   * @param string                      $rootPathEntities
   * @param array<string, class-string> $bundles
   */
  public function __construct(
    private readonly string $rootPathEntities,
    private readonly array $bundles = []
  ) {
    $this->filesystem = new Filesystem();
  }

  /**
   * @return $this
   *
   * @throws \ReflectionException
   */
  public function init(): InitialiseBundleEntities
  {
    $skeletonDirs = [];
    foreach ($this->bundles as $bundleName => $bundle)
    {
      if (preg_match('/Austral(.*Bundle)/iuU', $bundleName, $matches))
      {
        $reflectionClass = new \ReflectionClass($bundle);
        $bundleDir = dirname($reflectionClass->getFileName() ?: '');
        $skeletonDir = "{$bundleDir}/Skeleton/Entity";
        if (file_exists($skeletonDir) && is_dir($skeletonDir))
        {
          $skeletonDirs[$matches[1]] = $skeletonDir;
        }
      }
    }

    if (count($skeletonDirs))
    {
      if (!file_exists($this->rootPathEntities))
      {
        $this->filesystem->mkdir($this->rootPathEntities);
      }
      foreach ($skeletonDirs as $bundleName => $skeletonDir)
      {
        $entitiesPath = "{$this->rootPathEntities}/{$bundleName}";
        if (!file_exists($entitiesPath))
        {
          $this->filesystem->mkdir($entitiesPath);
        }
        $files = scandir($skeletonDir);
        foreach (is_array($files) ? $files : [] as $file)
        {
          if (!in_array($file, ['.', '..']))
          {
            if (!file_exists("{$entitiesPath}/{$file}"))
            {
              $fileContent = file_get_contents("{$skeletonDir}/{$file}");
              $fileContent = is_string($fileContent) ? $fileContent : '';
              $fileContent = str_replace('##php##', '<?php', $fileContent);
              file_put_contents("{$entitiesPath}/{$file}", $fileContent);
            }
          }
        }
      }
    }

    return $this;
  }
}
