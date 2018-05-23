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

namespace Elcodi\Admin\UserBundle\Builder;

use Elcodi\Component\Menu\Builder\Abstracts\AbstractMenuBuilder;
use Elcodi\Component\Menu\Builder\Interfaces\MenuBuilderInterface;
use Elcodi\Component\Menu\Entity\Menu\Interfaces\MenuInterface;
use Elcodi\Component\Menu\Factory\NodeFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MenuBuilder
 */
class MenuBuilder extends AbstractMenuBuilder implements MenuBuilderInterface {
	private $permissionsRepository;
	private $currentUser;
	private $resource = "customer";
	private $permissions = [
		'canRead' => false,
		'canCreate' => false,
		'canUpdate' => false,
		'canDelete' => false,
	];

	function __construct(NodeFactory $menuNodeFactory, ContainerInterface $container) {
		parent::__construct($menuNodeFactory);
		$this->permissionsRepository = $container->get('elcodi.repository.permission_group');
		$this->currentUser = $container->get('security.token_storage')->getToken()->getUser();

		$this->permissions = [
			'canRead' => $this->permissionsRepository->canReadEntity($this->resource, $this->currentUser),
			'canCreate' => $this->permissionsRepository->canCreateEntity($this->resource, $this->currentUser),
			'canUpdate' => $this->permissionsRepository->canUpdateEntity($this->resource, $this->currentUser),
			'canDelete' => $this->permissionsRepository->canDeleteEntity($this->resource, $this->currentUser),
		];
	}

	/**
	 * Build the menu
	 *
	 * @param MenuInterface $menu Menu
	 */
	public function build(MenuInterface $menu) {
		$node = $this
			->menuNodeFactory
			->create()
			->setName('admin.customer.plural');

		if ($this->permissions['canRead']) {
			$node
				->setCode('user')
				->setUrl('admin_customer_list')
				->setTag('order')
				->setPriority(16)
				->addSubnode(
					$this
						->menuNodeFactory
						->create()
						->setName('admin.customer.plural')
						->setCode('bullhorn')
						->setUrl('admin_customer_list')
						->setTag('communication')
				)
				->addSubnode(
					$this
						->menuNodeFactory
						->create()
						->setName('admin.customer.category')
						->setCode('bullhorn')
						->setUrl('admin_customer_category_list')
						->setTag('communication')
				);

			$activeUrls = [
				'admin_customer_list',
				'admin_customer_category_list',
			];

			if ($this->permissions['canUpdate']) {
				$activeUrls[] = 'admin_customer_edit';
			}

			$menu
				->addSubnode($node->setActiveUrls($activeUrls));
		}
		// if ($this->permissions['canRead']) {
		//     $customerNode = $this
		//                         ->menuNodeFactory
		//                         ->create()
		//                         ->setName('admin.customer.plural')
		//                         ->setCode('user')
		//                         ->setUrl('admin_customer_list')
		//                         ->setTag('order')
		//                         ->setPriority(32);

		//     if ($this->permissions['canUpdate']) {
		//         $customerNode->setActiveUrls([
		//             'admin_customer_edit',
		//         ]);
		//     }

		//     $menu->addSubnode($customerNode);
		// }
	}
}
