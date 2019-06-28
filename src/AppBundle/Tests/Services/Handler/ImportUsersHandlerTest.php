<?php
/**
 * Created by anonymous
 * Date: 26/07/18
 * Time: 17:52
 */

namespace AppBundle\Tests\Services\Handler;

use AppBundle\Entity\PlusUser;
use AppBundle\Services\EmailSenders\AbstractSender;
use AppBundle\Services\Handler\EmailsFromStringHandler;
use AppBundle\Services\Handler\ImportUsersHandler;
use AppBundle\Services\ReportGenerator\XLSXReportGenerator;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use UserManagementBundle\Services\UserManagers\InvitationCreator;

/**
 * Class ImportUsersHandlerTest
 *
 * @group main
 */
class ImportUsersHandlerTest extends TestCase
{
    /**
     * @var EmailsFromStringHandler|m\MockInterface
     */
    private $emailsFromString;

    /**
     * @var InvitationCreator|m\MockInterface
     */
    private $invitationCreator;

    /**
     * @var LoggerInterface|m\MockInterface
     */
    private $logger;

    /**
     * @var XLSXReportGenerator|m\MockInterface
     */
    private $xlsGenerator;

    /**
     * @var AbstractSender|m\MockInterface
     */
    private $emailSender;

    /**
     * @var ImportUsersHandler
     */
    private $handler;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this
            ->setEmailsFromString()
            ->setInvitationCreator()
            ->setLogger()
            ->setXlsGenerator()
            ->setEmailSender();

        $this
            ->setHandler(
                (new ImportUsersHandler())
                    ->setEmailsFromString($this->getEmailsFromString())
                    ->setInvitationCreator($this->getInvitationCreator())
                    ->setLogger($this->getLogger())
                    ->setXlsGenerator($this->getXlsGenerator())
                    ->setEmailSender($this->getEmailSender())
            );
    }

    /**
     * @return void
     */
    public function testParseWithStringReturnSelf(): void
    {
        /** @var TokenStorage|m\MockInterface $tokenStorage */
        $tokenStorage = m::mock(TokenStorage::class);
        $token = m::mock(TokenInterface::class);
        $user = m::mock(PlusUser::class);
        $user
            ->shouldReceive('getEmail')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo');
        $tokenStorage
            ->shouldReceive('getToken')
            ->atLeast()
            ->withNoArgs()
            ->andReturn($token);
        $token
            ->shouldReceive('getUser')
            ->atLeast()
            ->withNoArgs()
            ->andReturn($user);
        $this->getHandler()->setTokenStorage($tokenStorage);

        $this->assertEquals($this->getHandler(), $this->getHandler()->handle('foo'));
    }

    /**
     * @return void
     */
    public function testParseWithStringThrowsException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('User should be authenticated');

        /** @var TokenStorage|m\MockInterface $tokenStorage */
        $tokenStorage = m::mock(TokenStorage::class);
        $token = m::mock(TokenInterface::class);
        $user = m::mock(PlusUser::class);
        $user
            ->shouldReceive('getEmail')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo');
        $tokenStorage
            ->shouldReceive('getToken')
            ->atLeast()
            ->withNoArgs()
            ->andReturn($token);
        $token
            ->shouldReceive('getUser')
            ->atLeast()
            ->withNoArgs()
            ->andReturn(null);
        $this->getHandler()->setTokenStorage($tokenStorage);

        $this->getHandler()->handle('foo');
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @return ImportUsersHandler
     */
    public function getHandler(): ImportUsersHandler
    {
        return $this->handler;
    }

    /**
     * @param ImportUsersHandler $handler
     *
     * @return ImportUsersHandlerTest
     */
    public function setHandler(ImportUsersHandler $handler): ImportUsersHandlerTest
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return EmailsFromStringHandler|m\MockInterface
     */
    public function getEmailsFromString(): EmailsFromStringHandler
    {
        return $this->emailsFromString;
    }

    /**
     * @return ImportUsersHandlerTest
     */
    public function setEmailsFromString(): ImportUsersHandlerTest
    {
        $this->emailsFromString = m::mock(EmailsFromStringHandler::class);
        $this->getEmailsFromString()
            ->shouldReceive('parse')
            ->atLeast()
            ->with('foo')
            ->andReturnSelf();
        $this->getEmailsFromString()
            ->shouldReceive('getEmails')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('bar');

        return $this;
    }

    /**
     * @return InvitationCreator|m\MockInterface
     */
    public function getInvitationCreator(): InvitationCreator
    {
        return $this->invitationCreator;
    }

    /**
     * @return ImportUsersHandlerTest
     */
    public function setInvitationCreator(): ImportUsersHandlerTest
    {
        $this->invitationCreator = m::mock(InvitationCreator::class);
        $this->getInvitationCreator()
            ->shouldReceive('createByEmail')
            ->atLeast()
            ->with('bar')
            ->andReturnSelf();

        return $this;
    }

    /**
     * @return LoggerInterface|m\MockInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return ImportUsersHandlerTest
     */
    public function setLogger(): ImportUsersHandlerTest
    {
        $this->logger = m::mock(LoggerInterface::class);
        $this->getLogger()
            ->shouldReceive('alert', 'warning')
            ->atLeast()
            ->withAnyArgs()
            ->andReturnSelf();

        return $this;
    }

    /**
     * @return XLSXReportGenerator|m\MockInterface
     */
    public function getXlsGenerator(): XLSXReportGenerator
    {
        return $this->xlsGenerator;
    }

    /**
     * @return ImportUsersHandlerTest
     */
    public function setXlsGenerator(): ImportUsersHandlerTest
    {
        $this->xlsGenerator = m::mock(XLSXReportGenerator::class);
        $this->getXlsGenerator()
            ->shouldReceive('setFields', 'setData', 'setFileNamePrefix', 'setSheetName', 'generate')
            ->atLeast()
            ->withAnyArgs()
            ->andReturnSelf();
        $this->getXlsGenerator()
            ->shouldReceive('getFullFilePath')
            ->atLeast()
            ->withAnyArgs()
            ->andReturn('bar');

        return $this;
    }

    /**
     * @return AbstractSender|m\MockInterface
     */
    public function getEmailSender(): AbstractSender
    {
        return $this->emailSender;
    }

    /**
     * @return ImportUsersHandlerTest
     */
    public function setEmailSender(): ImportUsersHandlerTest
    {
        $this->emailSender = m::mock(AbstractSender::class);
        $this->getEmailSender()
            ->shouldReceive('sendEmailByAddress')
            ->atLeast()
            ->with('foo', 'users_import_report', 'en_US', ['attachments' => ['bar']])
            ->andReturnSelf();

        return $this;
    }
}
