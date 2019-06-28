<?php
/**
 * Created by anonymous
 * Date: 08/03/18
 * Time: 10:41
 */

namespace AppBundle\Tests\Services\Handler;

use AppBundle\Services\CurrentSiteGetter;
use AppBundle\Services\Handler\MailgunVariablesHandler;
use AppBundle\Structs\MailgunVariables;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class MailgunVariablesHandlerTest
 */
class MailgunVariablesHandlerTest extends TestCase
{
    /**
     * @var MailgunVariables|m\MockInterface
     */
    private $mailgunVariables;

    /**
     * @var CurrentSiteGetter|m\MockInterface
     */
    private $currentSiteGetter;

    /**
     * @var MailgunVariablesHandler
     */
    private $handler;

    /**
     * @throws \ReflectionException
     *
     * @return void
     */
    public function setUp()
    {
        $this
            ->setCurrentSiteGetter()
            ->setMailgunVariables();

        $this->handler = new MailgunVariablesHandler();
        $reflected = new \ReflectionClass($this->handler);
        $property = $reflected->getProperty('currentSiteGetter');
        $property->setAccessible(true);
        $property->setValue($this->handler, $this->getCurrentSiteGetter());
        $reflected = new \ReflectionClass($this->handler);
        $property = $reflected->getProperty('mailgunVariables');
        $property->setAccessible(true);
        $property->setValue($this->handler, $this->getMailgunVariables());
    }

    /**
     * @return void
     */
    public function testSetMailgunVariablesWithMailgunVariablesReturnSelf()
    {
        $this->assertEquals($this->handler, $this->handler->setMailgunVariables($this->getMailgunVariables()));
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @return MailgunVariables|m\MockInterface
     */
    private function getMailgunVariables(): MailgunVariables
    {
        return $this->mailgunVariables;
    }

    /**
     * @return MailgunVariablesHandlerTest
     */
    private function setMailgunVariables(): MailgunVariablesHandlerTest
    {
        $this->mailgunVariables = m::mock(MailgunVariables::class);
        $this->mailgunVariables
            ->shouldReceive('setDistributionName')
            ->atLeast()
            ->with('foo')
            ->andReturnSelf();

        return $this;
    }

    /**
     * @return CurrentSiteGetter|m\MockInterface
     */
    private function getCurrentSiteGetter(): CurrentSiteGetter
    {
        return $this->currentSiteGetter;
    }

    /**
     * @return MailgunVariablesHandlerTest
     */
    private function setCurrentSiteGetter(): MailgunVariablesHandlerTest
    {
        $this->currentSiteGetter = m::mock(CurrentSiteGetter::class);
        $this->currentSiteGetter
            ->shouldReceive('getDistributionName')
            ->atLeast()
            ->withNoArgs()
            ->andReturn('foo');

        return $this;
    }
}
