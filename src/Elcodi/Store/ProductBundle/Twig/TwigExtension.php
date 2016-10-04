<?php

namespace Elcodi\Store\ProductBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

class TwigExtension extends Twig_Extension {

	/**
	 * Return the functions registered as twig extensions
	 *
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('file_exists', 'file_exists'),
		);
	}

	public function getName() {
		return 'twig_extension';
	}
}

?>