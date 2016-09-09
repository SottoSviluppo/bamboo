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

namespace Elcodi\Store\CoreBundle\Controller;

use Elcodi\Store\CoreBundle\Controller\Traits\TemplateRenderTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Utils controller
 *
 */
class UtilsController extends Controller {
	// use TemplateRenderTrait;

	/**
	 *
	 * @return Response Response
	 *
	 * @Route(
	 *      path = "/is_logged",
	 *      name = "store_is_logged",
	 *      methods = {"GET"}
	 * )
	 */
	public function isLoggedAction() {
		$user = $this->getUser();
		if ($user)
			return new JsonResponse(array('status' => 'yes'));
		return new JsonResponse(array('status' => 'no'));
	}
}
