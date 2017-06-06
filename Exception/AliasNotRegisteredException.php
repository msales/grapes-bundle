<?php
namespace Msales\GrapesBundle\Exception;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class AliasNotRegisteredException extends ServiceNotFoundException
{
    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        parent::__construct($alias);
    }
}
