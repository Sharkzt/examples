<?php


namespace AppBundle\Tests\Security\Voter;

use AppBundle\Entity\PlusUser;
use AppBundle\Security\Voter\ObjectHasOwnerInterface;
use AppBundle\Security\Voter\ObjectHasOwnerVoter;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Role\Role;
use WordpressIntegrationBundle\Security\Authentication\Token\UserToken;

class ObjectHasOwnerVoterTest extends TestCase
{

    /**
     * @return array
     */
    public function getTestSupportsData()
    {
        $subjectMock = m::mock(ObjectHasOwnerInterface::class);

        return [
            [ObjectHasOwnerVoter::VIEW, $subjectMock, true],
            [ObjectHasOwnerVoter::EDIT, $subjectMock, true],
            ['show', new $subjectMock, false],
            [ObjectHasOwnerVoter::EDIT, new \stdClass(), false],
            ['show', new \stdClass(), false],
        ];
    }

    /**
     * @dataProvider getTestSupportsData
     *
     * @param string  $attribute
     * @param object  $subject
     * @param boolean $expected
     */
    public function testSupports($attribute, $subject, $expected)
    {
        $voter = new ObjectHasOwnerVoter();
        $reflectionClass = new \ReflectionClass($voter);
        $reflectionMethod = $reflectionClass->getMethod('supports');
        $reflectionMethod->setAccessible(true);

        $this->assertEquals($expected, $reflectionMethod->invoke($voter, $attribute, $subject));
    }

    public function getTestVoteOnAttribute()
    {
        /**
         * @var PlusUser|m\MockInterface
         */
        $plusUserMock = m::mock(PlusUser::class);

        return [
            [
                ObjectHasOwnerVoter::VIEW,
                m::mock(ObjectHasOwnerInterface::class)->shouldReceive('getOwner')->andReturn($plusUserMock)->getMock(),
                m::mock(TokenInterface::class)
                    ->shouldReceive('getUser')->andReturn($plusUserMock)
                    ->shouldReceive('getRoles')->andReturn([])
                    ->getMock(),
                true
            ],[
                ObjectHasOwnerVoter::VIEW,
                m::mock(ObjectHasOwnerInterface::class)->shouldReceive('getOwner')->andReturn($plusUserMock)->getMock(),
                (new UserToken(['ROLE_ADMINISTRATOR']))->setUser(m::mock(PlusUser::class)),
                true
            ],[
                ObjectHasOwnerVoter::VIEW,
                m::mock(ObjectHasOwnerInterface::class)->shouldReceive('getOwner')->andReturn($plusUserMock)->getMock(),
                (new UserToken(['ROLE_PUSHER']))->setUser(m::mock(PlusUser::class)),
                false
            ],[
                ObjectHasOwnerVoter::VIEW,
                m::mock(ObjectHasOwnerInterface::class)->shouldReceive('getOwner')->andReturn(null)->getMock(),
                (new UserToken(['ROLE_PUSHER']))->setUser(m::mock(PlusUser::class)),
                false
            ]
        ];
    }

    /**
     * @dataProvider getTestVoteOnAttribute
     *
     * @param string         $attribute
     * @param object         $subject
     * @param TokenInterface $token
     * @param boolean        $expected
     */
    public function testVoteOnAttribute($attribute, $subject, $token, $expected)
    {
        $voter = new ObjectHasOwnerVoter();
        $reflectionClass = new \ReflectionClass($voter);
        $reflectionMethod = $reflectionClass->getMethod('voteOnAttribute');
        $reflectionMethod->setAccessible(true);

        /**
         * @var AccessDecisionManagerInterface|m\MockInterface $decisionManagerMock
         */
        $decisionManagerMock = m::mock(AccessDecisionManagerInterface::class);
        $decisionManagerMock->shouldReceive('decide')
            ->withArgs(function (TokenInterface $token, array $expectedRoles) {
                /**
                 * @var Role[] $actualRoles
                 */
                $actualRoles = $token->getRoles();
                foreach ($expectedRoles as $expectedRole) {
                    foreach ($actualRoles as $actualRole) {
                        if ($expectedRole === $actualRole->getRole()) {
                            return true;
                        }
                    }
                }

                return false;
            })
            ->andReturn(true);
        $decisionManagerMock->shouldReceive('decide')
            ->andReturn(false);
        $voter->setDecisionManager($decisionManagerMock);

        $this->assertEquals($expected, $reflectionMethod->invoke($voter, $attribute, $subject, $token));
    }

}
