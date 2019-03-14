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

namespace Elcodi\Store\UserBundle\EventListener;

use Elcodi\Component\Page\Repository\PageRepository;
use Elcodi\Component\Store\Entity\Interfaces\StoreInterface;
use Elcodi\Component\User\Event\CustomerRegisterEvent;
use Elcodi\Store\CoreBundle\Services\TemplateLocator;
use Elcodi\Store\PageBundle\EventListener\Abstracts\AbstractEmailSenderEventListener;
use Monolog\Logger;
use Swift_Mailer;
use Twig_Environment;

/**
 * Class SendRegistrationEmailEventListener
 */
class SendRegistrationEmailEventListener extends AbstractEmailSenderEventListener {

	/**
	 * @var Swift_Mailer
	 *
	 * Mailer
	 */
	protected $mailer;

	/**
	 * @var Twig_Environment
	 *
	 * Twig
	 */
	protected $twig;

	/**
	 * @var PageRepository
	 *
	 * Page repository
	 */
	protected $pageRepository;

	/**
	 * @var StoreInterface
	 *
	 * Store
	 */
	protected $store;

	/**
	 * @var Logger
	 */
	protected $logger;

	/**
	 * sender email notification
	 * @var string
	 */
	protected $notificationSenderEmail;

	public function __construct(
		Swift_Mailer $mailer,
		Twig_Environment $twig,
		PageRepository $pageRepository,
		StoreInterface $store,
		TemplateLocator $templateLocator,
		Logger $logger,
		$notificationSenderEmail
	) {
		$this->mailer = $mailer;
		$this->twig = $twig;
		$this->pageRepository = $pageRepository;
		$this->store = $store;
		$this->templateLocator = $templateLocator;
		$this->logger = $logger;
		$this->notificationSenderEmail = $notificationSenderEmail;
	}
	/**
	 * Send email
	 *
	 * @param CustomerRegisterEvent $event Event
	 */
	public function sendCustomerRegistrationEmail(CustomerRegisterEvent $event) {
		$customer = $event->getCustomer();

		$this->sendEmail(
			'customer_registration',
			[
				'customer' => $customer,
			],
			$customer->getEmail(), //receiver
			false,
			$this->notificationSenderEmail//sender
		);

		$this->sendEmailToAdmin($customer);
	}

	/**
	 * Send notification to admin for new customer registration
	 */
	public function sendEmailToAdmin($customer) {

		$this->sendEmail(
			'customer_registration_admin',
			[
				'customer' => $customer,
			],
			$this->store->getEmail(), //receiver
			false,
			$this->notificationSenderEmail//sender
		);

	}

}
