<?php
namespace Msales\GrapesBundle\Abstraction;

use Msales\GrapesBundle\Exception\AliasNotRegisteredException;

interface ServiceGroupProviderInterface
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
     * @param string $alias
     *
     * @return object[] services identified by alias
     *
     * @throws AliasNotRegisteredException
     */
    public function get($alias);
}
