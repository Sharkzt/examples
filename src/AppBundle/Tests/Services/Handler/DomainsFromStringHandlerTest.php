<?php

namespace AppBundle\Tests\Services\Handler;

use AppBundle\Services\Handler\DomainsFromStringHandler;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class DomainsFromStringHandlerTest
 *
 * @group main
 */
class DomainsFromStringHandlerTest extends TestCase
{
    /**
     * @var DomainsFromStringHandler
     */
    private $handler;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->setHandler();
    }

    /**
     * @return void
     */
    public function testGetDomainsThrowException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No emails parsed from input string');

        $this->getHandler()->getDomains();
    }

    /**
     * @return void
     */
    public function testParseWithStringReturnSelf(): void
    {
        $this->assertEquals($this->getHandler(), $this->getHandler()->parse(' foo.com,    bar, q baz '));
        $this->assertCount(3, $this->getHandler()->getDomains());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @return DomainsFromStringHandler
     */
    public function getHandler(): DomainsFromStringHandler
    {
        return $this->handler;
    }

    /**
     * @return DomainsFromStringHandlerTest
     */
    public function setHandler(): DomainsFromStringHandlerTest
    {
        $this->handler = new DomainsFromStringHandler();

        return $this;
    }
}
