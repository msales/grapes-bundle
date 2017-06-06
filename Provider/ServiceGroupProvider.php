<?php
namespace Msales\GrapesBundle\Provider;

use Msales\GrapesBundle\Abstraction\ServiceGroupProviderInterface;
use Msales\GrapesBundle\Exception\AliasNotRegisteredException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ServiceGroupProvider implements ContainerAwareInterface, ServiceGroupProviderInterface
{
    use ContainerAwareTrait;

    /** @var array */
    protected $services = [];

    /** @var array */
    private $defaultGroup = [];

    /**
     * @param string $alias
     * @param string $taggedServiceID
     * @param bool   $isDefault
     */
    public function register($alias, $taggedServiceID, $isDefault = false)
    {
        $this->services[$alias][] = $taggedServiceID;
        if ($isDefault) {
            $this->defaultGroup[] = $taggedServiceID;
        }
    }

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return isset($this->services[$alias]);
    }

    /**
     * @param string $alias
     *
     * @return object[] services identified by alias
     *
     * @throws AliasNotRegisteredException
     */
    public function get($alias)
    {
        $loadedServices = [];

        if ($this->has($alias)) {
            foreach ($this->services[$alias] as $serviceId) {
                $loadedServices[] = $this->getService($serviceId);
            }

            return $loadedServices;
        }

        if (!$this->has($alias) && $this->hasDefaultServices()) {
            return $this->getServices($this->defaultGroup);
        }

        throw new AliasNotRegisteredException($alias);
    }

    /**
     * @param array $serviceIDs
     *
     * @return array
     */
    private function getServices($serviceIDs)
    {
        $loadedServices = [];
        foreach ($serviceIDs as $defaultServiceId) {
            $loadedServices[] = $this->getService($defaultServiceId);
        }

        return $loadedServices;
    }

    /**
     * @param string $serviceId
     *
     * @return object
     */
    private function getService($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @return bool
     */
    private function hasDefaultServices()
    {
        return !empty($this->defaultGroup);
    }
}
