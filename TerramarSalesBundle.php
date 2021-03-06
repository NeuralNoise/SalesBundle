<?php

namespace Terramar\Bundle\SalesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Terramar\Bundle\SalesBundle\DependencyInjection\Compiler\RegisterAlertFactoriesPass;
use Terramar\Bundle\SalesBundle\DependencyInjection\Compiler\OverrideServiceDefinitionsPass;

class TerramarSalesBundle extends Bundle
{
    public function boot()
    {
        Type::addType('enum.terramar.sales.contract_status',    'Terramar\Bundle\SalesBundle\DbalType\ContractStatusEnumType');
        Type::addType('enum.terramar.sales.found_by_type',      'Terramar\Bundle\SalesBundle\DbalType\FoundByTypeEnumType');
        Type::addType('enum.terramar.sales.billing_frequency',  'Terramar\Bundle\SalesBundle\DbalType\BillingFrequencyEnumType');
        Type::addType('enum.terramar.sales.invoice_status',     'Terramar\Bundle\SalesBundle\DbalType\InvoiceStatusEnumType');
        Type::addType('enum.terramar.sales.invoice_type',       'Terramar\Bundle\SalesBundle\DbalType\InvoiceTypeEnumType');
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new OverrideServiceDefinitionsPass());
    }
}
