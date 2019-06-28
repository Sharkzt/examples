<?php


namespace AppBundle\Listener;

use AppBundle\StaticClasses\JsonResponder;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class InjectLoggerListener
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     *
     * @return $this;
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        JsonResponder::$logger = $this->logger;
    }

}
