<?php

namespace Elcodi\Common\TranslationBundle;

use Elcodi\Common\TranslationBundle\CompilerPass\MappingCompilerPass;
use Elcodi\Common\TranslationBundle\DependencyInjection\Compiler\RegisterMappingPass;
use Elcodi\Common\TranslationBundle\DependencyInjection\Compiler\TranslatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle main class.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class LexikTranslationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());
        $container->addCompilerPass(new RegisterMappingPass());
        $container->addCompilerPass(new MappingCompilerPass());
    }
}
