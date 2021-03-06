<?php

namespace Terramar\Bundle\SalesBundle\DbalType;

use Orkestra\Common\DbalType\AbstractEnumType;

/**
 * Invoice Type EnumType
 *
 * Provides integration for the Invoice Type enumeration and Doctrine DBAL
 */
class InvoiceTypeEnumType extends AbstractEnumType
{
    /**
     * @var string The unique name for this EnumType
     */
    protected $name = 'enum.terramar.sales.invoice_type';

    /**
     * @var string The fully qualified class name of the Enum that this class wraps
     */
    protected $class = 'Terramar\Bundle\SalesBundle\Entity\Invoice\InvoiceType';
}
