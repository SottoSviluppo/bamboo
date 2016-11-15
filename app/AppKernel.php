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

use Mmoreram\SymfonyBundleDependencies\CachedBundleDependenciesResolver;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    use CachedBundleDependenciesResolver;

    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    public function registerBundles()
    {
        $bundles = [

            /**
             * Symfony dependencies
             */
            'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
            'Symfony\Bundle\SecurityBundle\SecurityBundle',
            'Symfony\Bundle\TwigBundle\TwigBundle',
            'Symfony\Bundle\MonologBundle\MonologBundle',
            'Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle',
            'Symfony\Bundle\AsseticBundle\AsseticBundle',

            /**
             * Third-party dependencies
             */
            'Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle',
            'Doctrine\Bundle\DoctrineBundle\DoctrineBundle',
            'Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle',
            'Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle',
            'Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle',

            'Knp\Bundle\GaufretteBundle\KnpGaufretteBundle',
            'Ornicar\GravatarBundle\OrnicarGravatarBundle',
            'HWI\Bundle\OAuthBundle\HWIOAuthBundle',
            'Snc\RedisBundle\SncRedisBundle',
            'JMS\I18nRoutingBundle\JMSI18nRoutingBundle',
            'Mmoreram\ControllerExtraBundle\ControllerExtraBundle',
            'Mmoreram\CacheFlushBundle\CacheFlushBundle',
            'Mmoreram\HttpHeadersBundle\HttpHeadersBundle',
            'FOS\ElasticaBundle\FOSElasticaBundle',
            'Knp\Bundle\PaginatorBundle\KnpPaginatorBundle',
            'Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle',

            /**
             * Elcodi core bundles
             */
            'Elcodi\Bundle\CoreBundle\ElcodiCoreBundle',
            'Elcodi\Bundle\LanguageBundle\ElcodiLanguageBundle',
            'Elcodi\Bundle\CartBundle\ElcodiCartBundle',
            'Elcodi\Bundle\CartCouponBundle\ElcodiCartCouponBundle',
            'Elcodi\Bundle\TaxBundle\ElcodiTaxBundle',
            'Elcodi\Bundle\CouponBundle\ElcodiCouponBundle',
            'Elcodi\Bundle\BannerBundle\ElcodiBannerBundle',
            'Elcodi\Bundle\CurrencyBundle\ElcodiCurrencyBundle',
            'Elcodi\Bundle\UserBundle\ElcodiUserBundle',
            'Elcodi\Bundle\GeoBundle\ElcodiGeoBundle',
            'Elcodi\Bundle\ProductBundle\ElcodiProductBundle',
            'Elcodi\Bundle\AttributeBundle\ElcodiAttributeBundle',
            'Elcodi\Bundle\MediaBundle\ElcodiMediaBundle',
            'Elcodi\Bundle\RuleBundle\ElcodiRuleBundle',
            'Elcodi\Bundle\NewsletterBundle\ElcodiNewsletterBundle',
            'Elcodi\Bundle\MenuBundle\ElcodiMenuBundle',
            'Elcodi\Bundle\EntityTranslatorBundle\ElcodiEntityTranslatorBundle',
            'Elcodi\Bundle\StateTransitionMachineBundle\ElcodiStateTransitionMachineBundle',
            'Elcodi\Bundle\PageBundle\ElcodiPageBundle',
            'Elcodi\Bundle\MetricBundle\ElcodiMetricBundle',
            'Elcodi\Bundle\PluginBundle\ElcodiPluginBundle',
            'Elcodi\Bundle\CommentBundle\ElcodiCommentBundle',
            'Elcodi\Bundle\ZoneBundle\ElcodiZoneBundle',
            'Elcodi\Bundle\CartShippingBundle\ElcodiCartShippingBundle',
            'Elcodi\Bundle\ShippingBundle\ElcodiShippingBundle',
            'Elcodi\Bundle\SitemapBundle\ElcodiSitemapBundle',
            'Elcodi\Bundle\PaymentBundle\ElcodiPaymentBundle',
            'Elcodi\Bundle\StoreBundle\ElcodiStoreBundle',

            /**
             * Elcodi store bundle
             */
            'Elcodi\Store\CoreBundle\StoreCoreBundle',
            'Elcodi\Store\ProductBundle\StoreProductBundle',
            'Elcodi\Store\UserBundle\StoreUserBundle',
            'Elcodi\Store\GeoBundle\StoreGeoBundle',
            'Elcodi\Store\CartBundle\StoreCartBundle',
            'Elcodi\Store\CurrencyBundle\StoreCurrencyBundle',
            'Elcodi\Store\CartCouponBundle\StoreCartCouponBundle',
            'Elcodi\Store\ConnectBundle\StoreConnectBundle',
            'Elcodi\Store\MetricBundle\StoreMetricBundle',
            'Elcodi\Store\PageBundle\StorePageBundle',
            'Elcodi\Store\LanguageBundle\StoreLanguageBundle',
            'Elcodi\Store\OverrideBundle\StoreOverrideBundle',
            'Elcodi\Store\FallbackBundle\StoreFallbackBundle',
            'Elcodi\Store\SearchBundle\StoreSearchBundle',

            /**
             * Elcodi admin bundles
             */
            'Elcodi\Admin\CoreBundle\AdminCoreBundle',
            'Elcodi\Admin\UserBundle\AdminUserBundle',
            'Elcodi\Admin\AttributeBundle\AdminAttributeBundle',
            'Elcodi\Admin\BannerBundle\AdminBannerBundle',
            'Elcodi\Admin\CartBundle\AdminCartBundle',
            'Elcodi\Admin\CouponBundle\AdminCouponBundle',
            'Elcodi\Admin\CurrencyBundle\AdminCurrencyBundle',
            'Elcodi\Admin\LanguageBundle\AdminLanguageBundle',
            'Elcodi\Admin\TaxBundle\AdminTaxBundle',
            'Elcodi\Admin\MediaBundle\AdminMediaBundle',
            'Elcodi\Admin\NewsletterBundle\AdminNewsletterBundle',
            'Elcodi\Admin\ProductBundle\AdminProductBundle',
            'Elcodi\Admin\PageBundle\AdminPageBundle',
            'Elcodi\Admin\TemplateBundle\AdminTemplateBundle',
            'Elcodi\Admin\MetricBundle\AdminMetricBundle',
            'Elcodi\Admin\PluginBundle\AdminPluginBundle',
            'Elcodi\Admin\ShippingBundle\AdminShippingBundle',
            'Elcodi\Admin\GeoBundle\AdminGeoBundle',
            'Elcodi\Admin\PaymentBundle\AdminPaymentBundle',
            'Elcodi\Admin\StoreBundle\AdminStoreBundle',
            'Elcodi\Admin\SearchBundle\AdminSearchBundle',
            'Elcodi\Admin\SocialBundle\AdminSocialBundle',

            /**
             * Elcodi common bundle
             */
            'Elcodi\Common\FirewallBundle\ElcodiFirewallBundle',
            'Elcodi\Common\CommonBundle\ElcodiCommonBundle',

            /**
             * Elcodi Plugins
             */
            'Elcodi\Plugin\GoogleAnalyticsBundle\ElcodiGoogleAnalyticsBundle',
            'Elcodi\Plugin\PinterestBundle\ElcodiPinterestBundle',
            'Elcodi\Plugin\ProductCsvBundle\ElcodiProductCsvBundle',
            'Elcodi\Plugin\StoreSetupWizardBundle\ElcodiStoreSetupWizardBundle',
            'Elcodi\Plugin\DisqusBundle\ElcodiDisqusBundle',
            'Elcodi\Plugin\TwitterBundle\ElcodiTwitterBundle',
            'Elcodi\Plugin\FacebookBundle\ElcodiFacebookBundle',
            'Elcodi\Plugin\StoreTemplateBundle\StoreTemplateBundle',
            'Elcodi\Plugin\PaypalWebCheckoutBundle\ElcodiPaypalWebCheckoutBundle',
            'Elcodi\Plugin\FreePaymentBundle\ElcodiFreePaymentBundle',
            'Elcodi\Plugin\StripeBundle\ElcodiStripeBundle',
            'Elcodi\Plugin\CustomShippingBundle\ElcodiCustomShippingBundle',
            'Elcodi\Plugin\BankwireBundle\ElcodiBankwireBundle',
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = 'Elcodi\Bundle\FixturesBoosterBundle\ElcodiFixturesBoosterBundle';
            $bundles[] = 'Elcodi\Bridge\BehatBridgeBundle\BehatBridgeBundle';
            $bundles[] = 'Symfony\Bundle\WebProfilerBundle\WebProfilerBundle';
            $bundles[] = 'Sensio\Bundle\DistributionBundle\SensioDistributionBundle';
        }

        if (class_exists('Visithor\Bundle\VisithorBundle')) {
            $bundles[] = 'Visithor\Bundle\VisithorBundle';
            $bundles[] = 'Elcodi\Bridge\VisithorBridgeBundle\ElcodiVisithorBridgeBundle';
        }

        $pluginBundles = $this->getConfiguredBundles(__DIR__ . '/config/enabled_bundles.yml');
        if (is_array($pluginBundles)) {
            $bundles = array_merge($bundles, $pluginBundles);
        }

        return $this
            ->getBundleInstances(
                $this,
                $bundles
            );
    }

    protected function getConfiguredBundles($configurationPath)
    {
        if (!file_exists($configurationPath)) {
            return array();
        }

        return Symfony\Component\Yaml\Yaml::parse(file_get_contents($configurationPath))['enabled_bundles']; // Replace with better logic
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    public function getCacheDir()
    {
        // return '/dev/shm/project/cache/' .  $this->environment;
        if (in_array($this->environment, array('dev', 'test' /*, 'prod'*/))) {
            return '/dev/shm/project/cache/' . $this->environment;
        }

        return parent::getCacheDir();
    }

    public function getLogDir()
    {
        // return '/dev/shm/project/logs';
        if (in_array($this->environment, array('dev', 'test' /*, 'prod'*/))) {
            return '/dev/shm/project/logs';
        }

        return parent::getLogDir();
    }
}
