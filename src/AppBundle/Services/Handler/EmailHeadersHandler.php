<?php

namespace AppBundle\Services\Handler;

use AppBundle\Entity\PlusSetting;
use AppBundle\Services\App\AbstractManager;

/**
 * Class EmailHeadersHandler
 */
class EmailHeadersHandler extends AbstractManager
{
    /**
     * @var string
     */
    private $from = '';

    /**
     * @var string
     */
    private $replyTo = '';

    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     *
     * @return EmailHeadersHandler
     */
    public function setFrom(string $from): EmailHeadersHandler
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    /**
     * @param string $replyTo
     *
     * @return EmailHeadersHandler
     */
    public function setReplyTo(string $replyTo): EmailHeadersHandler
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     *
     * @return EmailHeadersHandler
     */
    public function setFromEmail(string $fromEmail): EmailHeadersHandler
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return EmailHeadersHandler
     */
    public function handle(): EmailHeadersHandler
    {
        $from = $this->getEntityManager()
            ->getRepository(PlusSetting::class)
            ->getPlusSettingValueByName(PlusSetting::EMAIL_SENDER_NAME)
            ->getValue()
        ;
        $this
            ->setFrom($from)
            ->setReplyTo($this->getFromEmail())
        ;

        return $this;
    }
}
