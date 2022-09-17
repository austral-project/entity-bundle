<?php
/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\Entity\Interfaces;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Austral Entity File Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface FileInterface
{

  /**
   * @return int|string
   */
  public function getObjectIdToFile();

  /**
   * @return array
   */
  public function getUploadFiles(): array;

  /**
   * @param string $fieldname
   * @param UploadedFile|null $uploadedFile
   *
   * @return FileInterface
   */
  public function setUploadFileByFieldname(string $fieldname, ?UploadedFile $uploadedFile = null): FileInterface;

  /**
   * @param string $fieldname
   *
   * @return UploadedFile|null
   */
  public function getUploadFileByFieldname(string $fieldname): ?UploadedFile;

  /**
   * @param string $fieldname
   *
   * @return bool
   */
  public function getDeleteFileByFieldname(string $fieldname): bool;

  /**
   * @param string $fieldname
   * @param $value
   *
   * @return $this
   */
  public function setDeleteFileByFieldname(string $fieldname, $value): FileInterface;

  /**
   * @return array
   */
  public function getDeleteFiles(): array;

  /**
   * @param $deleteFiles
   *
   * @return FileInterface
   */
  public function setDeleteFiles($deleteFiles): FileInterface;

  /**
   * @param string $fieldname
   *
   * @return mixed
   * @throws \Exception
   */
  public function getValueByFieldname(string $fieldname);

  /**
   * @param string $fieldname
   * @param $value
   *
   * @return FileInterface
   * @throws \Exception
   */
  public function setValueByFieldname(string $fieldname, $value = null);

}
