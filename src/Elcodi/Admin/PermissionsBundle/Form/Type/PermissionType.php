<?php

namespace Elcodi\Admin\PermissionsBundle\Form\Type;

use Elcodi\Component\Core\Factory\Traits\FactoryTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PermissionType extends AbstractType
{
    use FactoryTrait;

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    /*public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'empty_data' => function () {
                $this
                    ->factory
                    ->create();
            },
            'data_class' => $this
                ->factory
                ->getEntityNamespace(),
        ]);
    }*/
    
    /**
     * Buildform function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resource', 'choice', [
                'choices' => [
                    'product' => 'Products',
                    'category' => 'Categories',
                    'order' => 'Orders'
                ]
            ])
            ->add('can_read', 'checkbox', [
                'label' => 'admin.permissions.field.canRead.title',
                'required' => false
            ])
            ->add('can_create', 'checkbox', [
                'label' => 'admin.permissions.field.canCreate.title',
                'required' => false
            ])
            ->add('can_update', 'checkbox', [
                'label' => 'admin.permissions.field.canUpdate.title',
                'required' => false
            ])
            ->add('can_delete', 'checkbox', [
                'label' => 'admin.permissions.field.canDelete.title',
                'required' => false
            ]);
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
        return 'elcodi_admin_permissions_form_type_permission';
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