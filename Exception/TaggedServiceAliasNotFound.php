<?php
namespace Msales\GrapesBundle\Exception;

use Exception;

class TaggedServiceAliasNotFound extends Exception
{
    /**
     * @param string         $tag
     * @param string         $alias
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct(string $tag, string $alias, int $code = 0, Exception $previous = null)
    {
        $message = sprintf('You have requested a non-existent service with tag "%s" and alias "%s".', $tag, $alias);
        parent::__construct($message, $code, $previous);
    }
}
