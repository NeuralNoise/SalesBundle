<?php

namespace Terramar\Bundle\SalesBundle\Form\Invoice;

use Symfony\Component\Form\AbstractType;
use Terramar\Bundle\SalesBundle\Model\SalesProfileInterface;
use Orkestra\Transactor\Entity\Transaction\NetworkType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
    protected $profile;

    public function __construct(SalesProfileInterface $profile)
    {
        $this->profile = $profile;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $profile = $this->profile;

        $excludeTypes = array(NetworkType::MFA, NetworkType::SWIPED);

        if (false === $options['allow_points']) {
            $excludeTypes[] = NetworkType::POINTS;
        }

        if (false === $options['allow_cash']) {
            $excludeTypes[] = NetworkType::CASH;
        }

        if (false === $options['allow_check']) {
            $excludeTypes[] = NetworkType::CHECK;
        }

        $builder->add('method', 'enum', array(
                'enum' => 'Orkestra\Transactor\Entity\Transaction\NetworkType',
                'exclude' => $excludeTypes,
                'labels' => array(
                    NetworkType::POINTS => 'Account Credit'
                )
            ))
            ->add('account', 'entity_choice', array(
                'class' => 'Orkestra\Transactor\Entity\AbstractAccount',
                'query_builder' => function() use ($profile) {
                    return $profile->getAccounts();
                }
            ))
            ->add('amount');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => false,
            'data_class' => 'Terramar\Bundle\SalesBundle\Model\Invoice\Payment',
            'allow_points' => true,
            'allow_cash' => true,
            'allow_check' => true
        ));
    }

    public function getName()
    {
        return 'payment';
    }
}
