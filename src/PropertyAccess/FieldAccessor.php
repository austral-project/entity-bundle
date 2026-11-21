<?php

/*
 * This file is part of the Austral Entity Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\EntityBundle\PropertyAccess;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyWriteInfo;

/**
 * Field access with the PropertyAccess rules (get/is/has/can, setters, adders,
 * public properties) but never magic methods. Classes can be checked without an instance.
 *
 * Without magic, an object can be read or written from its own __get/__set without looping.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
final class FieldAccessor
{
  /**
   * Same context as Symfony's PropertyAccessor, magic methods excluded.
   */
  private const array READ_CONTEXT = [
    'enable_getter_setter_extraction' => true,
    'enable_magic_methods_extraction' => ReflectionExtractor::DISALLOW_MAGIC_METHODS,
    'enable_constructor_extraction' => false,
  ];

  private const array WRITE_CONTEXT = self::READ_CONTEXT + [
    'enable_adder_remover_extraction' => true,
  ];

  /**
   * @var ReflectionExtractor|null
   */
  private static ?ReflectionExtractor $extractor = null;

  /**
   * @var PropertyAccessorInterface|null
   */
  private static ?PropertyAccessorInterface $propertyAccessor = null;

  /**
   * @param object|class-string $objectOrClass
   */
  public static function isReadable(object|string $objectOrClass, string $fieldname): bool
  {
    $classname = is_object($objectOrClass) ? $objectOrClass::class : $objectOrClass;

    return null !== self::extractor()->getReadInfo($classname, $fieldname, self::READ_CONTEXT);
  }

  /**
   * @param object|class-string $objectOrClass
   */
  public static function isWritable(object|string $objectOrClass, string $fieldname): bool
  {
    $classname = is_object($objectOrClass) ? $objectOrClass::class : $objectOrClass;
    $writeInfo = self::extractor()->getWriteInfo($classname, $fieldname, self::WRITE_CONTEXT);

    return null !== $writeInfo && PropertyWriteInfo::TYPE_NONE !== $writeInfo->getType();
  }

  /**
   * getValue
   *
   * @param object $object
   * @param string $fieldname
   *
   * @return mixed
   */
  public static function getValue(object $object, string $fieldname): mixed
  {
    return self::propertyAccessor()->getValue($object, $fieldname);
  }

  /**
   * setValue
   *
   * @param object $object
   * @param string $fieldname
   * @param mixed  $value
   *
   * @return void
   */
  public static function setValue(object $object, string $fieldname, mixed $value): void
  {
    self::propertyAccessor()->setValue($object, $fieldname, $value);
  }

  /**
   * extractor
   *
   * @return ReflectionExtractor
   */
  private static function extractor(): ReflectionExtractor
  {
    return self::$extractor ??= new ReflectionExtractor(magicMethodsFlags: ReflectionExtractor::DISALLOW_MAGIC_METHODS);
  }

  /**
   * propertyAccessor
   *
   * @return PropertyAccessorInterface
   */
  private static function propertyAccessor(): PropertyAccessorInterface
  {
    return self::$propertyAccessor ??= PropertyAccess::createPropertyAccessorBuilder()
      ->disableMagicMethods()
      ->getPropertyAccessor();
  }
}
