<?php
/**
 * Created by anonymous
 * Date: 24/07/18
 * Time: 18:52
 */

namespace AppBundle\Tests\Services\Handler;

use AppBundle\Services\Handler\EmailsFromStringHandler;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class EmailsFromStringHandlerTest
 *
 * @group main
 */
class EmailsFromStringHandlerTest extends TestCase
{
    /**
     * @var EmailsFromStringHandler
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
    public function testGetEmailsThrowException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No emails parsed from input string');

        $this->getHandler()->getEmails();
    }

    /**
     * @return void
     */
    public function testParseWithStringReturnSelf(): void
    {
        $this->assertEquals($this->getHandler(), $this->getHandler()->parse(' foo    bar baz '));
        $this->assertCount(3, $this->getHandler()->getEmails());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        m::close();
    }

    /**
     * @return EmailsFromStringHandler
     */
    public function getHandler(): EmailsFromStringHandler
    {
        return $this->handler;
    }

    /**
     * @return EmailsFromStringHandlerTest
     */
    public function setHandler(): EmailsFromStringHandlerTest
    {
        $this->handler = new EmailsFromStringHandler();

        return $this;
    }
}
