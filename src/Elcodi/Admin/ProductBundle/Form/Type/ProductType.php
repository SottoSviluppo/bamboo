<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Admin\ProductBundle\Form\Type;

use Elcodi\Admin\ProductBundle\Validation\MinimumMoney;
use Elcodi\Component\Core\Factory\Traits\FactoryTrait;
use Elcodi\Component\EntityTranslator\EventListener\Traits\EntityTranslatableFormTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class ProductType
 */
class ProductType extends AbstractType
{
    use EntityTranslatableFormTrait, FactoryTrait;

    /**
     * @var string
     *
     * Manufacturer namespace
     */
    protected $manufacturerNamespace;

    /**
     * @var string
     *
     * Category namespace
     */
    protected $categoryNamespace;

    /**
     * @var string
     *
     * Image namespace
     */
    protected $imageNamespace;

    /**
     * @var string
     *
     * Tax namespace
     */
    protected $taxNamespace;

    /**
     * Construct
     *
     * @param string $manufacturerNamespace Manufacturer namespace
     * @param string $categoryNamespace     Category namespace
     * @param string $imageNamespace        Image namespace
     */
    public function __construct(
        $manufacturerNamespace,
        $categoryNamespace,
        $imageNamespace,
        $taxNamespace
    ) {
        $this->manufacturerNamespace = $manufacturerNamespace;
        $this->categoryNamespace = $categoryNamespace;
        $this->imageNamespace = $imageNamespace;
        $this->taxNamespace = $taxNamespace;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
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
            ->add('name', 'text', [
                'required' => true,
                'constraints' => [
                    new Constraints\Length(
                        [
                            'max' => 65,
                        ]
                    ),
                ],
            ])
            ->add('slug', 'text', [
                'required' => true,
                'constraints' => [
                    new Constraints\Length(
                        [
                            'max' => 65,
                        ]
                    ),
                ],
            ])
            ->add('description', 'ckeditor', [
                'required' => true,
                'config_name' => 'my_config',
            ])
            ->add('barcode', 'text', [
                'required' => false,
            ])
            ->add('showInHome', 'checkbox', [
                'required' => false,
            ])
            ->add('stock', 'hidden', [
                'required' => true,
            ])
            ->add('sku', 'text', [
                'required' => false,
            ])
            ->add('price', 'money_object', [
                'required' => true,
                'constraints' => [
                    new MinimumMoney([
                        'value' => 0,
                    ]),
                ],
            ])
            ->add('reducedPrice', 'money_object', [
                'required' => false,
                'constraints' => [
                    new MinimumMoney([
                        'value' => 0,
                    ]),
                ],
            ])
            ->add('tax', 'entity', [
                'class' => $this->taxNamespace,
                'required' => false,
                'multiple' => false,
            ])
            ->add('imagesSort', 'text', [
                'required' => false,
            ])
            ->add('enabled', 'checkbox', [
                'required' => false,
            ])
            ->add('private', 'checkbox', [
                'required' => false,
            ])
            ->add('height', 'number', [
                'required' => false,
            ])
            ->add('width', 'number', [
                'required' => false,
            ])
            ->add('depth', 'number', [
                'required' => false,
            ])
            ->add('weight', 'number', [
                'required' => false,
            ])
            ->add('metaTitle', 'text', [
                'required' => false,
            ])
            ->add('metaDescription', 'text', [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(
                        [
                            'max' => 159,
                        ]
                    ),
                ],
            ])
            ->add('metaKeywords', 'text', [
                'required' => false,
            ])
            ->add('stock', 'number', [
                'required' => false,
            ])
            ->add('manufacturer', 'entity', [
                'class' => $this->manufacturerNamespace,
                'required' => false,
                'multiple' => false,
            ])
            ->add('principalCategory', 'entity', [
                'class' => $this->categoryNamespace,
                'required' => true,
                'multiple' => false,
            ])
            ->add('categories', 'entity', [
                'class' => $this->categoryNamespace,
                'required' => false,
                'multiple' => true,
            ])
            ->add('images', 'entity', [
                'class' => $this->imageNamespace,
                'required' => false,
                'property' => 'id',
                'multiple' => true,
                'expanded' => false,
            ]);

        $builder->addEventSubscriber($this->getEntityTranslatorFormEventListener());
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
        return 'elcodi_admin_product_form_type_product';
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
