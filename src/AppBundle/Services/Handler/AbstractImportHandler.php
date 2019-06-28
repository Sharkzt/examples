<?php

namespace AppBundle\Services\Handler;

use AppBundle\Entity\PlusUser;
use AppBundle\Services\App\AbstractManager;
use AppBundle\Services\EmailSenders\AbstractSender;
use AppBundle\Services\ReportGenerator\XLSXReportGenerator;
use Psr\Log\LoggerInterface;
use UserManagementBundle\Services\UserManagers\InvitationCreator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class AbstractImportHandler
 */
abstract class AbstractImportHandler extends AbstractManager
{
    /**
     * @var EmailsFromStringHandler
     */
    private $emailsFromString;

    /**
     * @var InvitationCreator
     */
    private $invitationCreator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var XLSXReportGenerator
     */
    private $xlsGenerator;

    /**
     * @var AbstractSender
     */
    private $emailSender;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @return EmailsFromStringHandler
     */
    public function getEmailsFromString(): EmailsFromStringHandler
    {
        return $this->emailsFromString;
    }

    /**
     * @param EmailsFromStringHandler $emailsFromString
     *
     * @return AbstractImportHandler
     */
    public function setEmailsFromString(EmailsFromStringHandler $emailsFromString): AbstractImportHandler
    {
        $this->emailsFromString = $emailsFromString;

        return $this;
    }

    /**
     * @return InvitationCreator
     */
    public function getInvitationCreator(): InvitationCreator
    {
        return $this->invitationCreator;
    }

    /**
     * @param InvitationCreator $invitationCreator
     *
     * @return AbstractImportHandler
     */
    public function setInvitationCreator(InvitationCreator $invitationCreator): AbstractImportHandler
    {
        $this->invitationCreator = $invitationCreator;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return AbstractImportHandler
     */
    public function setLogger(LoggerInterface $logger): AbstractImportHandler
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return XLSXReportGenerator
     */
    public function getXlsGenerator(): XLSXReportGenerator
    {
        return $this->xlsGenerator;
    }

    /**
     * @param XLSXReportGenerator $xlsGenerator
     *
     * @return AbstractImportHandler
     */
    public function setXlsGenerator(XLSXReportGenerator $xlsGenerator): AbstractImportHandler
    {
        $this->xlsGenerator = $xlsGenerator;

        return $this;
    }

    /**
     * @return AbstractSender
     */
    public function getEmailSender(): AbstractSender
    {
        return $this->emailSender;
    }

    /**
     * @param AbstractSender $emailSender
     *
     * @return AbstractImportHandler
     */
    public function setEmailSender(AbstractSender $emailSender): AbstractImportHandler
    {
        $this->emailSender = $emailSender;

        return $this;
    }

    /**
     * @return TokenStorage
     */
    public function getTokenStorage(): TokenStorage
    {
        return $this->tokenStorage;
    }

    /**
     * @param TokenStorage $tokenStorage
     *
     * @return AbstractImportHandler
     */
    public function setTokenStorage(TokenStorage $tokenStorage): AbstractImportHandler
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * @param string        $source
     * @param PlusUser|null $user
     *
     * @return AbstractImportHandler
     */
    abstract public function handle(string $source, PlusUser $user = null): AbstractImportHandler;

    /**
     * @param iterable $fields
     * @param iterable $data
     *
     * @return AbstractImportHandler
     */
    protected function generateReport(iterable $fields, iterable $data): AbstractImportHandler
    {
        $this->getXlsGenerator()
            ->setFields($fields)
            ->setData($data)
            ->setFileNamePrefix('users_import')
            ->setSheetName('Users import')
            ->generate();

        return $this;
    }
}
