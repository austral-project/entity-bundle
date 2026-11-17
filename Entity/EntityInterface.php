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
 * Kept for backward compatibility with 3.x projects.
 * @deprecated since 4.0, use \Austral\Contracts\Entity\EntityInterface instead.
 * @template T of object
 *
 * @extends \Austral\Contracts\Entity\EntityInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface EntityInterface extends \Austral\Contracts\Entity\EntityInterface
{
}
