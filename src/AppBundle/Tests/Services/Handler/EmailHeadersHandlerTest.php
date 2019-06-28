<?php

namespace AppBundle\Tests\Services\Handler;

use AppBundle\Entity\PlusSetting;
use AppBundle\Repository\PlusSettingsRepository;
use AppBundle\Services\App\AbstractManager;
use AppBundle\Services\Handler\EmailHeadersHandler;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class EmailHeadersHandlerTest
 *
 * @group main
 */
class EmailHeadersHandlerTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EmailHeadersHandler
     */
    private $handler;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setEm();

        $this->setHandler(
            (new EmailHeadersHandler())
                ->setFromEmail('foo@bar.com')
                ->setEntityManager($this->getEm())
        );
    }

    /**
     * @return void
     */
    public function testHandleReturnSelf(): void
    {
        $this->assertEquals($this->getHandler(), $this->getHandler()->handle());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return EmailHeadersHandlerTest
     */
    public function setEm(): EmailHeadersHandlerTest
    {
        $this->em = m::mock(EntityManager::class);
        $repo = m::mock(PlusSettingsRepository::class);
        $setting = m::mock(PlusSetting::class);
        $setting
            ->shouldReceive('getValue')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo@bar.com');
        $repo
            ->shouldReceive('getPlusSettingValueByName')
            ->atLeast()
            ->withAnyArgs()
            ->andReturn($setting);
        $this->em
            ->shouldReceive('getRepository')
            ->atLeast()
            ->with(PlusSetting::class)
            ->andReturn($repo);

        return $this;
    }

    /**
     * @return EmailHeadersHandler
     */
    public function getHandler(): EmailHeadersHandler
    {
        return $this->handler;
    }

    /**
     * @param EmailHeadersHandler|AbstractManager $handler
     *
     * @return EmailHeadersHandlerTest
     */
    public function setHandler(EmailHeadersHandler $handler): EmailHeadersHandlerTest
    {
        $this->handler = $handler;

        return $this;
    }
}
