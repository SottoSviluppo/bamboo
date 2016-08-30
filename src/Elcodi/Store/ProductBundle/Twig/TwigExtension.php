<?php

namespace Elcodi\Store\ProductBundle\Twig;

use Twig_Extension;
use Twig_Function_Function;

class TwigExtension extends Twig_Extension {

	/**
	 * Return the functions registered as twig extensions
	 *
	 * @return array
	 */
	public function getFunctions() {
		return array(
			'file_exists' => new Twig_Function_Function('file_exists'),
		);
	}

	public function getName() {
		return 'twig_extension';
	}
}

?>