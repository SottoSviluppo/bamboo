<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2016 Elcodi Networks S.L.
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

namespace Elcodi\Common\TranslationBundle\CompilerPass;

use Mmoreram\SimpleDoctrineMapping\CompilerPass\Abstracts\AbstractMappingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MappingCompilerPass.
 */
class MappingCompilerPass extends AbstractMappingCompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this
            ->addEntityMapping(
                $container,
                'default',
                'Elcodi\Common\TranslationBundle\Entity\TransUnit',
                '@LexikTranslationBundle/Resources/config/doctrine/TransUnit.orm.xml',
                true
            )
            ->addEntityMapping(
                $container,
                'default',
                'Elcodi\Common\TranslationBundle\Entity\Translation',
                '@LexikTranslationBundle/Resources/config/doctrine/Translation.orm.xml',
                true
            )
            ->addEntityMapping(
                $container,
                'default',
                'Elcodi\Common\TranslationBundle\Entity\File',
                '@LexikTranslationBundle/Resources/config/doctrine/File.orm.xml',
                true
            )
        ;
    }

}
