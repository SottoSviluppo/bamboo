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

namespace Elcodi\Admin\SocialBundle;

use Mmoreram\SymfonyBundleDependencies\DependentBundleInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Elcodi\Bundle\CoreBundle\Abstracts\AbstractElcodiBundle;
use Elcodi\Component\Admin\Interfaces\AdminInterface;
use Elcodi\Admin\SocialBundle\DependencyInjection\ElcodiSocialExtension;
use Elcodi\Admin\SocialBundle\CompilerPass\MappingCompilerPass;

/**
 * Class AdminSocialBundle
 */

class AdminSocialBundle extends AbstractElcodiBundle
{
// class AdminSocialBundle
//     extends AbstractElcodiBundle
//     implements AdminInterface, DependentBundleInterface
// {
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MappingCompilerPass());
    }


    /**
     * Returns the bundle's container extension.
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension()
    {
        return new ElcodiSocialExtension();
    }

    // /**
    //  * Create instance of current bundle, and return dependent bundle namespaces
    //  *
    //  * @return array Bundle instances
    //  */
    // public static function getBundleDependencies(KernelInterface $kernel)
    // {
    //     return [
    //         'Elcodi\Bundle\CoreBundle\ElcodiCoreBundle',
    //     ];
    // }
}