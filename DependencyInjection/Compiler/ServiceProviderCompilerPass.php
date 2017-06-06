<?php
namespace Msales\GrapesBundle\DependencyInjection\Compiler;

use Msales\GrapesBundle\DependencyInjection\ServiceProviderDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceProviderCompilerPass implements CompilerPassInterface
{
    const TAG_ALIAS_DEFAULT = 'alias';
    const TAG_DEFAULT = 'default';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        /*
         * Step 1: getting the providers
         */
        $providerServices = $this->findProviderServices($container);
        $groupProviderServices = $this->findGroupProviderServices($container);

        /*
         * Step 2: for each provider, finding all services it should provide
         * and process the provider
         */
        $this->processServiceProviders($providerServices, $container, true);
        $this->processServiceProviders($groupProviderServices, $container, false);
    }

    /**
     * @param array            $providerServices
     * @param ContainerBuilder $container
     * @param bool             $shouldWrap
     */
    private function processServiceProviders(array $providerServices, ContainerBuilder $container, $shouldWrap)
    {
        foreach ($providerServices as $servicesProviderId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['related_tag'])) {
                    $relatedTag = $tag['related_tag'];
                    $tagAlias = $this->getTagAlias($tag);

                    $providerDefinition = $shouldWrap
                        ? $this->wrapServiceProviderDefinition($container->getDefinition($servicesProviderId))
                        : $providerDefinition = $container->getDefinition($servicesProviderId);

                    $this->processServiceProvider($container, $providerDefinition, $relatedTag, $tagAlias);
                }
            }
        }
    }

    /**
     * Wrap service provider definition into a decorator class.
     *
     * The decorator makes sure there is only one default service registered for a service provider.
     *
     * @param Definition $providerDefinition
     *
     * @return ServiceProviderDefinition
     */
    private function wrapServiceProviderDefinition(Definition $providerDefinition)
    {
        return new ServiceProviderDefinition($providerDefinition);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function findProviderServices(ContainerBuilder $container)
    {
        return $container->findTaggedServiceIds('grapes.service_provider');
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function findGroupProviderServices(ContainerBuilder $container)
    {
        return $container->findTaggedServiceIds('grapes.service_group_provider');
    }

    /**
     * @param array $tag
     *
     * @return string
     */
    private function getTagAlias(array $tag)
    {
        if (isset($tag['tag_alias'])) {
            return $tag['tag_alias'];
        }

        return self::TAG_ALIAS_DEFAULT;
    }

    /**
     * @param array $relatedServiceTag
     *
     * @return bool
     */
    private function isDefault($relatedServiceTag)
    {
        return isset($relatedServiceTag[self::TAG_DEFAULT])
            && true === $relatedServiceTag[self::TAG_DEFAULT];
    }

    /**
     * @param ContainerBuilder                     $container
     * @param Definition|ServiceProviderDefinition $servicesProviderDefinition
     * @param string                               $relatedTag
     * @param string                               $tagAlias
     */
    private function processServiceProvider(
        ContainerBuilder $container,
        $servicesProviderDefinition,
        $relatedTag,
        $tagAlias
    ) {
        $relatedTagServices = $container->findTaggedServiceIds($relatedTag);
        foreach ($relatedTagServices as $relatedServiceId => $relatedServiceTags) {
            foreach ($relatedServiceTags as $relatesServiceTag) {
                if (isset($relatesServiceTag[$tagAlias])) {
                    $relatedServiceAlias = $relatesServiceTag[$tagAlias];
                    $servicesProviderDefinition->addMethodCall(
                        'register',
                        [$relatedServiceAlias, $relatedServiceId, $this->isDefault($relatesServiceTag)]
                    );
                }
            }
        }
    }
}
