<?php

namespace FollowersBundle\Tests\Services\Handlers;

use AppBundle\Entity\PlusContribution;
use AppBundle\Entity\PlusEmployeeFollower;
use AppBundle\Entity\PlusEmployee;
use AppBundle\Entity\PlusPersonalGoal;
use AppBundle\Entity\PlusUser;
use FollowersBundle\Services\Handlers\ContributionEmailHandler;
use FollowersBundle\Services\Handlers\SharedContributionHandler;
use PHPUnit\Framework\TestCase;
use Mockery as m;

/**
 * Class SharedContributionHandlerTest
 *
 * @group main
 */
class SharedContributionHandlerTest extends TestCase
{
    /**
     * @var PlusContribution|m\MockInterface
     */
    private $contribution;

    /**
     * @var ContributionEmailHandler|m\MockInterface
     */
    private $emailHandler;

    /**
     * @var SharedContributionHandler
     */
    private $handler;

    /**
     * @return void
     */
    public function setUp()
    {
        $this
            ->setContribution()
            ->setEmailHandler();

        $this->handler = new SharedContributionHandler();

        $reflected = new \ReflectionClass($this->handler);
        $property = $reflected->getProperty('emailHandler');
        $property->setAccessible(true);
        $property->setValue($this->handler, $this->emailHandler);
    }

    /**
     * @return void
     */
    public function testSendEmailsWithNonReceiverAndReturnThis()
    {
        $this->getContribution()
            ->shouldReceive('getIsShared')
            ->atLeast()
            ->andReturnFalse();

        $this->assertEquals($this->handler, $this->handler->sendEmails($this->getContribution()));
    }

    /**
     * @return void
     */
    public function testSendEmailsWithReceiverAndReturnThis()
    {
        $this->getContribution()
            ->shouldReceive('getIsShared')
            ->atLeast()
            ->andReturnTrue();

        $this->assertEquals($this->handler, $this->handler->sendEmails($this->getContribution()));
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @return PlusContribution|m\MockInterface
     */
    public function getContribution(): PlusContribution
    {
        return $this->contribution;
    }

    /**
     * @return SharedContributionHandlerTest
     */
    public function setContribution(): SharedContributionHandlerTest
    {
        $this->contribution = m::mock(PlusContribution::class);

        $this->contribution->shouldReceive('getEmployee')
            ->andReturn((m::mock(PlusEmployee::class))
                ->shouldReceive('getUser')
                ->andReturn((m::mock(PlusUser::class))
                    ->shouldReceive('getFollowers')
                    ->andReturn([
                        (m::mock(PlusUser::class))
                        ->shouldReceive('getCurrentEmployee')
                        ->andReturn((m::mock(PlusEmployee::class)))
                        ->getMock()
                    ])
                    ->getMock()
                )
                ->getMock()
            )
        ;

        return $this;
    }

    /**
     * @return ContributionEmailHandler|m\MockInterface
     */
    public function getEmailHandler(): ContributionEmailHandler
    {
        return $this->emailHandler;
    }

    /**
     * @return SharedContributionHandlerTest
     */
    public function setEmailHandler(): SharedContributionHandlerTest
    {
        $this->emailHandler = m::mock(ContributionEmailHandler::class);
        $this->emailHandler
            ->shouldReceive('handle')
            ->atLeast()
            ->andReturnSelf();

        return $this;
    }
}
