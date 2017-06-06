<?php
namespace Msales\GrapesBundle\Abstraction;

use Msales\GrapesBundle\Exception\AliasNotRegisteredException;

interface ServiceProviderInterface
{
    /**
     * @param string $alias
     * @param string $taggedServiceID
     * @param bool   $isDefault
     */
    public function register($alias, $taggedServiceID, $isDefault = false);

    /**
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias);

    /**
     * @param $alias
     *
     * @return object service identified by alias
     *
     * @throws AliasNotRegisteredException
     */
    public function get($alias);
}
