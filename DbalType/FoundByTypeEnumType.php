<?php

namespace Terramar\Bundle\SalesBundle\DbalType;

use Orkestra\Common\DbalType\AbstractEnumType;

/**
 * Found By Type EnumType
 *
 * Provides integration for the Found By Type enumeration and Doctrine DBAL
 */
class FoundByTypeEnumType extends AbstractEnumType
{
    /**
     * @var string The unique name for this EnumType
     */
    protected $name = 'enum.terramar.sales.found_by_type';

    /**
     * @var string The fully qualified class name of the Enum that this class wraps
     */
    protected $class = 'Terramar\Bundle\SalesBundle\Entity\Contract\FoundByType';
}
