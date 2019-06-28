<?php

namespace FollowersBundle\Tests\Services\Handlers;

use FollowersBundle\Services\Handlers\FollowedEmailHandler;

/**
 * Class FollowedEmailHandlerTest
 *
 * @group main
 */
class FollowedEmailHandlerTest extends AbstractEmailHandlerTest
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setHandler(
            (new FollowedEmailHandler())
            ->setContentHandler($this->getContentHandler())
            ->setSender($this->getSender())
            ->setUrlHandler($this->getUrlHandler())
        );
    }
}
