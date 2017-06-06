<?php
namespace Msales\GrapesBundle;

use Msales\GrapesBundle\DependencyInjection\Compiler\ServiceProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MsalesGrapesBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServiceProviderCompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
