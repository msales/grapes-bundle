<?php
namespace Msales\GrapesBundle\DependencyInjection;

use Msales\GrapesBundle\Exception\DefaultServiceAlreadySetException;
use Symfony\Component\DependencyInjection\Definition;

class ServiceProviderDefinition
{
    /** @var bool */
    private $hasDefaultService = false;

    /** @var Definition */
    private $serviceProviderDefinition;

    public function __construct(Definition $serviceProviderDefinition)
    {
        $this->serviceProviderDefinition = $serviceProviderDefinition;
    }

    /**
     * Throws an exception at an attempt to add a second default service.
     * Delegates 'addMethodCall' call to the wrapped Definition instannce.
     *
     * @param string $method
     * @param array  $arguments [0 => service alias, 1 => service ID, 2 => $isDefault]
     *
     * @return Definition
     *
     * @throws DefaultServiceAlreadySetException
     */
    public function addMethodCall($method, array $arguments = [])
    {
        if ('register' === $method) {
            return $this->addMethodCallToRegister($method, $arguments);
        }

        return $this->serviceProviderDefinition->addMethodCall($method, $arguments);
    }

    /**
     * Guard against adding more than one default service.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return Definition
     */
    private function addMethodCallToRegister($method, array $arguments)
    {
        list($alias, $serviceId, $isDefault) = $arguments;

        if (isset($isDefault) && $isDefault === true) {
            if ($this->hasDefaultService) {
                throw new DefaultServiceAlreadySetException($serviceId, $alias);
            }
            $this->hasDefaultService = true;
        }

        return $this->serviceProviderDefinition->addMethodCall($method, $arguments);
    }
}
