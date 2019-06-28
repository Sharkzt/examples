<?php

namespace FollowersBundle\Services\Handlers;

use AppBundle\Entity\PlusEmployee;
use AppBundle\Services\CurrentSiteGetter;
use FollowersBundle\Containers\Context\AbstractSendingContext;
use FollowersBundle\Containers\Context\ShareContributionSendingContext;
use FollowersBundle\Services\EmailSenders\AbstractFollowersEmailSender;
use FollowersBundle\Services\Settings\FollowersSettings;

/**
 * Class AbstractEmailHandler
 */
abstract class AbstractEmailHandler
{
    /**
     * @var AbstractEmailContentHandler
     */
    private $contentHandler;

    /**
     * @var AbstractFollowersEmailSender
     */
    private $sender;

    /**
     * @var FollowersSettings
     */
    private $settings;

    /**
     * @var CurrentSiteGetter
     */
    private $urlHandler;

    /**
     * @return AbstractEmailContentHandler
     */
    public function getContentHandler(): AbstractEmailContentHandler
    {
        return $this->contentHandler;
    }

    /**
     * @param AbstractEmailContentHandler $contentHandler
     * @return AbstractEmailHandler
     */
    public function setContentHandler(AbstractEmailContentHandler $contentHandler): AbstractEmailHandler
    {
        $this->contentHandler = $contentHandler;

        return $this;
    }

    /**
     * @return AbstractFollowersEmailSender
     */
    public function getSender(): AbstractFollowersEmailSender
    {
        return $this->sender;
    }

    /**
     * @param AbstractFollowersEmailSender $sender
     * @return AbstractEmailHandler
     */
    public function setSender(AbstractFollowersEmailSender $sender): AbstractEmailHandler
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return FollowersSettings
     */
    public function getSettings(): FollowersSettings
    {
        return $this->settings;
    }

    /**
     * @param FollowersSettings $settings
     * @return AbstractEmailHandler
     */
    public function setSettings(FollowersSettings $settings): AbstractEmailHandler
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return CurrentSiteGetter
     */
    public function getUrlHandler(): CurrentSiteGetter
    {
        return $this->urlHandler;
    }

    /**
     * @param CurrentSiteGetter $urlHandler
     * @return AbstractEmailHandler
     */
    public function setUrlHandler(CurrentSiteGetter $urlHandler): AbstractEmailHandler
    {
        $this->urlHandler = $urlHandler;

        return $this;
    }

    /**
     * @return AbstractSendingContext|ShareContributionSendingContext
     */
    public function getSendingContext(): AbstractSendingContext
    {
        return $this->getSender()->getContext();
    }

    /**
     * @param PlusEmployee $employee
     * @param PlusEmployee $featuredEmployee
     *
     * @return AbstractEmailHandler
     */
    abstract public function handle(PlusEmployee $employee, PlusEmployee $featuredEmployee): AbstractEmailHandler;

    /**
     * @param PlusEmployee $employee
     *
     * @return bool
     */
    abstract public function isEmailReceiver(PlusEmployee $employee): bool;
}
