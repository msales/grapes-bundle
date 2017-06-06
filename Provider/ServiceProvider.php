<?php
namespace Msales\GrapesBundle\Provider;

use Msales\GrapesBundle\Abstraction\ServiceProviderInterface;
use Msales\GrapesBundle\Exception\AliasNotRegisteredException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ServiceProvider implements ContainerAwareInterface, ServiceProviderInterface
{
    use ContainerAwareTrait;

    /** @var array */
    protected $services = [];

    /** @var string */
    protected $defaultService;

    /**
     * @param      $alias
     * @param      $taggedServiceID
     * @param bool $isDefault
     */
    public function register($alias, $taggedServiceID, $isDefault = false)
    {
        $this->services[$alias] = $taggedServiceID;
        if ($isDefault) {
            $this->defaultService = $this->services[$alias];
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
     * @param $alias
     *
     * @return object service identified by alias
     *
     * @throws AliasNotRegisteredException
     */
    public function get($alias)
    {
        if ($this->has($alias)) {
            return $this->getRegisteredService($alias);
        }

        if (!$this->has($alias) && $this->hasDefaultService()) {
            return $this->getDefaultService();
        }

        throw new AliasNotRegisteredException($alias);
    }

    /**
     * @param string $alias
     *
     * @return object
     */
    private function getRegisteredService($alias)
    {
        return $this->container->get($this->services[$alias]);
    }

    /**
     * @return object
     */
    private function getDefaultService()
    {
        return $this->container->get($this->defaultService);
    }

    /**
     * @return bool
     */
    private function hasDefaultService()
    {
        return isset($this->defaultService);
    }
}
