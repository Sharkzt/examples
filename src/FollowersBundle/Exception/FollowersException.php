<?php

namespace FollowersBundle\Exception;

/**
 * Class FollowersException
 */
class FollowersException extends \Exception
{
    /**
     * FollowersException constructor.
     * @param string $message
     * @param int    $code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__.": [{$this->code}]: {$this->message}\n";
    }
}
