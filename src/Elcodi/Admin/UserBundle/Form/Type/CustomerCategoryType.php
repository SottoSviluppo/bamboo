<?php

namespace Elcodi\Admin\UserBundle\Form\Type;

use Elcodi\Component\Core\Factory\Traits\FactoryTrait;
use Elcodi\Component\EntityTranslator\EventListener\Traits\EntityTranslatableFormTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class CustomerCategoryType
 */
class CustomerCategoryType extends AbstractType {
	use EntityTranslatableFormTrait, FactoryTrait;

	public function __construct() {
	}

	/**
	 * Configures the options for this type.
	 *
	 * @param OptionsResolver $resolver The resolver for the options.
	 */
	public function configureOptions(OptionsResolver $resolver) {
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
	public function buildForm(FormBuilderInterface $builder, array $options) {
		/*$currentCategoryId = $builder
			->getData()
			->getId();*/

		$categoryNamespace = $this
			->factory
			->getEntityNamespace();

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
			->add('enabled', 'checkbox', [
				'required' => false,
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
	public function getBlockPrefix() {
		return 'elcodi_admin_user_form_type_customer_category';
	}

	/**
	 * Return unique name for this form
	 *
	 * @deprecated Deprecated since Symfony 2.8, to be removed from Symfony 3.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->getBlockPrefix();
	}
}
