<?php

namespace Elcodi\Admin\CouponBundle\Form\Type;

use Elcodi\Admin\ProductBundle\Validation\MinimumMoney;
use Elcodi\Component\Core\Factory\Traits\FactoryTrait;
use Elcodi\Component\Coupon\ElcodiCouponTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CouponGenerationType extends AbstractType
{

    public function __construct()
    {
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_data' => null,
            'data_class' => null,
        ]);
    }

    /**
     * Buildform function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        // ->add('name', 'text', [
        //     'required' => true,
        // ])
        ->add('coupon_campaign', 'entity', [
            'required' => false,
            'class' => 'Elcodi\Component\Coupon\Entity\CouponCampaign',
        ])
        // ->add('type', 'choice', [
        //     'required' => true,
        //     'choices' => [
        //         ElcodiCouponTypes::TYPE_AMOUNT => 'admin.coupon.field.type.options.fixed',
        //         ElcodiCouponTypes::TYPE_PERCENT => 'admin.coupon.field.type.options.percent',
        //     ],
        // ])
        // ->add('enforcement', 'choice', [
        //     'required' => true,
        //     'choices' => [
        //         ElcodiCouponTypes::ENFORCEMENT_MANUAL => 'admin.coupon.field.enforcement.options.manual',
        //         ElcodiCouponTypes::ENFORCEMENT_AUTOMATIC => 'admin.coupon.field.enforcement.options.automatic',
        //     ],
        // ])
            ->add('price', 'money_object', [
                'required' => false,
                'constraints' => [
                    new MinimumMoney([
                        'value' => 0,
                    ]),
                ],
            ])
        // ->add('discount', 'integer', [
        //     'required' => false,
        // ])
            ->add('count', 'integer', [
                'required' => false,
            ])
            ->add('chars', 'integer', [
                'required' => false,
            ])
        // ->add('countCustomer', 'integer', [
        //     'required' => false,
        // ])
        // ->add('used', 'integer', [
        //     'required' => false,
        // ])
        // ->add('priority', 'integer', [
        //     'required' => false,
        // ])
        // ->add('minimumPurchase', 'money_object', [
        //     'required' => false,
        //     'constraints' => [
        //         new MinimumMoney([
        //             'value' => 0,
        //         ]),
        //     ],
        // ])
            ->add('stackable', 'checkbox', [
                'required' => false,
            ])
            ->add('free_shipping', 'checkbox', [
                'required' => false,
            ])
            ->add('color', 'text', [
                'required' => false,
            ])
            ->add('validFrom', 'datetime', [
                'date_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('validTo', 'datetime', [
                'date_widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd',
                'time_widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'elcodi_admin_coupon_generation_form_type_coupon';
    }

    /**
     * Return unique name for this form
     *
     * @deprecated Deprecated since Symfony 2.8, to be removed from Symfony 3.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
