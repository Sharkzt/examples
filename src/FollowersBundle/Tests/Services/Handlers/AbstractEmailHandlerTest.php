<?php

namespace FollowersBundle\Tests\Services\Handlers;

use AppBundle\Entity\PlusEmployee;
use AppBundle\Entity\PlusUser;
use AppBundle\Entity\User;
use AppBundle\Services\CurrentSiteGetter;
use FollowersBundle\Containers\AbstractEmailContent;
use FollowersBundle\Containers\Context\AbstractSendingContext;
use FollowersBundle\Services\EmailSenders\AbstractFollowersEmailSender;
use FollowersBundle\Services\Handlers\AbstractEmailContentHandler;
use FollowersBundle\Services\Handlers\AbstractEmailHandler;
use FollowersBundle\Services\Settings\FollowersSettings;
use FollowersBundle\Services\TemplateParsers\AbstractTemplateParser;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class AbstractEmailHandlerTest
 */
abstract class AbstractEmailHandlerTest extends TestCase
{
    /**
     * @var AbstractEmailContentHandler|m\MockInterface
     */
    private $contentHandler;

    /**
     * @var AbstractFollowersEmailSender|m\MockInterface
     */
    private $sender;

    /**
     * @var PlusEmployee|m\MockInterface
     */
    private $employee;

    /**
     * @var FollowersSettings[]|m\MockInterface[]
     */
    private $settings;

    /**
     * @var CurrentSiteGetter|m\MockInterface
     */
    private $urlHandler;

    /**
     * @var AbstractEmailHandler
     */
    private $handler;

    /**
     * @return void
     */
    public function setUp()
    {
        $this
            ->setSender()
            ->setContentHandler()
            ->setSettings()
            ->setUrlHandler()
            ->setEmployee();
    }

    /**
     * @return void
     */
    public function testHandleWithPlusEmployeesAndReceiverReturnThis()
    {
        $this->getHandler()->setSettings($this->getTrueSettings());

        $this->assertEquals($this->getHandler(), $this->getHandler()->handle($this->getEmployee(), $this->getEmployee()));
    }

    /**
     * @return void
     */
    public function testHandleWithPlusEmployeesAndNotReceiverReturnThis()
    {
        $this->getHandler()->setSettings($this->getFalseSettings());

        $this->assertEquals($this->getHandler(), $this->getHandler()->handle($this->getEmployee(), $this->getEmployee()));
    }

    /**
     * @return void
     */
    public function testIsEmailReceiverWithPlusEmployeesReturnTrue()
    {
        $this->getHandler()->setSettings($this->getTrueSettings());

        $this->assertEquals(true, $this->getHandler()->isEmailReceiver($this->getEmployee()));
        $this->assertTrue($this->getHandler()->isEmailReceiver($this->getEmployee()));
    }

    /**
     * @return void
     */
    public function testIsEmailReceiverWithPlusEmployeesReturnFalse()
    {
        $this->getHandler()->setSettings($this->getFalseSettings());

        $this->assertEquals(false, $this->getHandler()->isEmailReceiver($this->getEmployee()));
        $this->assertFalse($this->getHandler()->isEmailReceiver($this->getEmployee()));
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @return AbstractEmailContentHandler
     */
    public function getContentHandler(): AbstractEmailContentHandler
    {
        return $this->contentHandler;
    }

    /**
     * @return AbstractEmailHandlerTest
     */
    public function setContentHandler(): AbstractEmailHandlerTest
    {
        $this->contentHandler = m::mock(AbstractEmailContentHandler::class);
        $content = m::mock(AbstractEmailContent::class);
        $content->shouldReceive('getSubject', 'getBody')->andReturn('foo');
        $this->contentHandler->shouldReceive('handle')->andReturn($this->contentHandler);
        $parser = m::mock(AbstractTemplateParser::class);
        $this->contentHandler->shouldReceive('getParser')->andReturn($parser);
        $this->contentHandler->shouldReceive('getParsedContent')->andReturn($content);

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
     * @return AbstractEmailHandlerTest
     */
    public function setSender(): AbstractEmailHandlerTest
    {
        $this->sender = m::mock(AbstractFollowersEmailSender::class);
        $context = m::mock(AbstractSendingContext::class);
        $context->shouldReceive('setBody', 'setSubject', 'setHeaders', 'setTags', 'setSlug', 'addTag', 'setSenderName', 'setReplyTo')->andReturn($context);
        $this->sender->shouldReceive('getContext')->andReturn($context);
        $this->sender->shouldReceive('sendEmailByUser', 'sendEmailByEmployeeAndUser')->andReturn($this->sender);

        return $this;
    }

    /**
     * @return PlusEmployee
     */
    public function getEmployee(): PlusEmployee
    {
        return $this->employee;
    }

    /**
     * @return AbstractEmailHandlerTest
     */
    public function setEmployee(): AbstractEmailHandlerTest
    {
        $this->employee = m::mock(PlusEmployee::class);
        $wtUser = m::mock(User::class);
        $wtUser
            ->shouldReceive('getUserEmail')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo');
        $user = m::mock(PlusUser::class);
        $user
            ->shouldReceive('getFirstName', 'getLastName', 'getEmail')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo');
        $user
            ->shouldReceive('getWTUser')
            ->atLeast()
            ->withNoArgs()
            ->andReturn($wtUser);
        $this->employee->shouldReceive('getUser')->andReturn($user);

        return $this;
    }

    /**
     * @return FollowersSettings
     */
    public function getTrueSettings(): FollowersSettings
    {
        return $this->settings[1];
    }

    /**
     * @return FollowersSettings
     */
    public function getFalseSettings(): FollowersSettings
    {
        return $this->settings[0];
    }

    /**
     * @return AbstractEmailHandlerTest
     */
    public function setSettings(): AbstractEmailHandlerTest
    {
        $this->settings[0] = m::mock(FollowersSettings::class);
        $this->settings[1] = m::mock(FollowersSettings::class);
        $this->settings[0]->shouldReceive('isFollowEmailReceiver', 'isShareContributionEmailReceiver')->andReturn(false);
        $this->settings[1]->shouldReceive('isFollowEmailReceiver', 'isShareContributionEmailReceiver')->andReturn(true);

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
     * @return AbstractEmailHandlerTest
     */
    public function setUrlHandler(): AbstractEmailHandlerTest
    {
        $this->urlHandler = m::mock(CurrentSiteGetter::class);
        $this->urlHandler->shouldReceive('getCurrentSite')->andReturn('foo');

        return $this;
    }

    /**
     * @return AbstractEmailHandler
     */
    public function getHandler(): AbstractEmailHandler
    {
        return $this->handler;
    }

    /**
     * @param AbstractEmailHandler $handler
     * @return AbstractEmailHandlerTest
     */
    public function setHandler(AbstractEmailHandler $handler): AbstractEmailHandlerTest
    {
        $this->handler = $handler;

        return $this;
    }
}
