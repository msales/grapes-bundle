<?php
namespace Msales\GrapesBundle\Exception;

use Exception;
use InvalidArgumentException;

class DefaultServiceAlreadySetException extends InvalidArgumentException
{
    /**
     * @param string         $tag
     * @param string         $alias
     * @param int            $code
     * @param Exception|null $previous
     */
    public function __construct(string $tag, string $alias, int $code = 0, Exception $previous = null)
    {
        $message = sprintf(
            'Service with the tag "%s" and alias "%s" cannot be marked as default. '
            . 'There already exists a service marked as default.',
            $tag,
            $alias
        );
        parent::__construct($message, $code, $previous);
    }
}
