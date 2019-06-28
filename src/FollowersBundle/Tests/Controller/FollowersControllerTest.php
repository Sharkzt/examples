<?php

namespace FollowersBundle\Tests\Controller;

use AppBundle\DataFixtures\TestData\LoadUsers;
use AppBundle\Entity\PlusEmployeeFollower;
use AppBundle\Entity\PlusEmployee;
use AppBundle\TestHelpers\TestCases\FunctionalTestCase;
use FollowersBundle\Event\FollowersEvent;

/**
 * Class FollowersControllerTest
 */
class FollowersControllerTest extends FunctionalTestCase
{
    /**
     * @var \Swift_Plugins_MessageLogger
     */
    private $mailLogger;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->mailLogger = $this->container->get('swiftmailer.mailer.default.plugin.messagelogger');
    }

    /**
     * @return void
     */
    public function testIsOwnerWithAdminReturnFalse()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $response = $this->makeJsonRequest('GET', "v2_followers_is_owner", ['user' => 1, 'department' => 1, 'employee' => 3], json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(false, $response['success']);
        $this->assertFalse($response['success']);
    }

    /**
     * @return void
     */
    public function testIsOwnerWithAdminReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $response = $this->makeJsonRequest('GET', "v2_followers_is_owner", ['user' => 1, 'department' => 1, 'employee' => 1], json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(true, $response['success']);
        $this->assertTrue($response['success']);
    }

    /**
     * @return void
     */
    public function testIsOwnerWithUserReturnFalse()
    {
        $this->configureLoggedInByJWT(LoadUsers::HEAD_USER_EMAIL);

        $response = $this->makeJsonRequest('GET', "v2_followers_is_owner", ['user' => 6, 'department' => 2, 'employee' => 3], json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(false, $response['success']);
        $this->assertFalse($response['success']);
    }

    /**
     * @return void
     */
    public function testIsOwnerWithUserReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::HEAD_USER_EMAIL);

        $response = $this->makeJsonRequest('GET', "v2_followers_is_owner", ['user' => 6, 'department' => 2, 'employee' => 7], json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(true, $response['success']);
        $this->assertTrue($response['success']);
    }

    /**
     * @return void
     */
    public function testFollowWithAdminReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $actualEvent = null;
        $eventListenerMock = function (FollowersEvent $event) use (&$actualEvent) {
            $actualEvent = $event;
        };
        $this->container->get('event_dispatcher')
            ->addListener(FollowersEvent::ON_FOLLOW, $eventListenerMock);
        $this->container->get('event_dispatcher')
            ->addListener(FollowersEvent::POST_FOLLOW, $eventListenerMock);

        $response = $this->makeJsonRequest('POST', "v2_followers_follow", [], \json_encode([
            'user' => 2,
            'department' => 3,
            'employee' => 1,
            'followerEmployee' => ['user' => 2, 'department' => 3],
        ]), ['HTTP_X-Locale' => 'en_US']);

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(true, $response['success']);
        $this->assertTrue($response['success']);

        $followedEmployee = $this->entityManager->getRepository(PlusEmployee::class)->findOneBy(['id' => 1]);
        /** @var PlusEmployeeFollower[] $followers */
        $followers = $followedEmployee->getFollowers();

        foreach ($followers as $follower) {
            $this->assertEquals(3, $follower->getFollower()->getId());
        }
        $this->assertCount(1, $followers);

        $messages = $this->mailLogger->getMessages();

        $this->assertCount(12, $messages[0]->getHeaders()->getAll());
        $this->assertEquals(['admin@test.com' => null], $messages[0]->getTo());

        /**
         * @var \Swift_Mime_Header[] $tags
         */
        $tags = $messages[0]->getHeaders()->getAll('o:tag');
        $this->assertEquals('followed_newFollower', $tags[0]->getFieldBody());
        $this->assertContains('followed_newFollower_', $tags[1]->getFieldBody());

        $this->assertEquals('second second is now following you', $messages[0]->getSubject());
        $this->assertContains('Open contributions', $messages[0]->getBody());
        $this->assertContains('Check out second second contributions to success now:', $messages[0]->getBody());
        $this->assertContains('second second is now following you. second works in Department 2; in Accounting.', $messages[0]->getBody());
        $this->assertContains('With second second you already count 1 followers', $messages[0]->getBody());
        $this->assertContains('Hi, admin admin!', $messages[0]->getBody());
        $mailgunVariables = json_decode($messages[0]->getHeaders()->get('v:x-custom-variables')->getFieldBody(), true);

        $this->assertArrayHasKey('distribution_domain', $mailgunVariables);
        $this->assertArrayHasKey('employee_id', $mailgunVariables);
        $this->assertArrayHasKey('campaign_tags', $mailgunVariables);
        $this->assertEquals(1, $mailgunVariables['employee_id']);
        $this->assertNull($mailgunVariables['campaign_tags']);
    }

    /**
     * @return void
     */
    public function testFollowWithUserReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $actualEvent = null;
        $eventListenerMock = function (FollowersEvent $event) use (&$actualEvent) {
            $actualEvent = $event;
        };
        $this->container->get('event_dispatcher')
            ->addListener(FollowersEvent::ON_FOLLOW, $eventListenerMock);
        $this->container->get('event_dispatcher')
            ->addListener(FollowersEvent::POST_FOLLOW, $eventListenerMock);

        $response = $this->makeJsonRequest('POST', "v2_followers_follow", [], \json_encode([
            'user' => 7,
            'department' => 2,
            'employee' => 1,
            'followerEmployee' => ['user' => 7, 'department' => 2],
        ]), ['HTTP_X-Locale' => 'ar_SA']);

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(true, $response['success']);
        $this->assertTrue($response['success']);

        $followedEmployee = $this->entityManager->getRepository(PlusEmployee::class)->findOneBy(['id' => 1]);
        /** @var PlusEmployeeFollower[] $followers */
        $followers = $followedEmployee->getFollowers();

        $this->assertCount(2, $followers);

        $messages = $this->mailLogger->getMessages();

        $this->assertCount(12, $messages[0]->getHeaders()->getAll());
        $this->assertEquals(['admin@test.com' => null], $messages[0]->getTo());

        /**
         * @var \Swift_Mime_Header[] $tags
         */
        $tags = $messages[0]->getHeaders()->getAll('o:tag');
        $this->assertEquals('followed_newFollower', $tags[0]->getFieldBody());
        $this->assertContains('followed_newFollower_', $tags[1]->getFieldBody());
        $this->assertEquals('multiDepartment multiDepartment Test is now following you', $messages[0]->getSubject());
        $this->assertContains('Test Open contributions', $messages[0]->getBody());
        $this->assertContains('Test multiDepartment multiDepartment is now following you', $messages[0]->getBody());
        $this->assertContains('Test If you like to contact multiDepartment multiDepartment, write to multi.department@test.com', $messages[0]->getBody());
        $this->assertContains('Test With multiDepartment multiDepartment you already count 2 followers.', $messages[0]->getBody());
        $this->assertContains('Test Hi, admin admin!', $messages[0]->getBody());
        $this->assertContains('Test Check out multiDepartment multiDepartment contributions now:', $messages[0]->getBody());
        $mailgunVariables = json_decode($messages[0]->getHeaders()->get('v:x-custom-variables')->getFieldBody(), true);

        $this->assertArrayHasKey('distribution_domain', $mailgunVariables);
        $this->assertArrayHasKey('employee_id', $mailgunVariables);
        $this->assertArrayHasKey('campaign_tags', $mailgunVariables);
        $this->assertEquals(1, $mailgunVariables['employee_id']);
        $this->assertNull($mailgunVariables['campaign_tags']);
    }

    /**
     * @return void
     */
    public function testIsFollowerWitUserLoggedInReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::HEAD_USER_EMAIL);

        $response = $this->makeJsonRequest('POST', "v2_followers_is_follower", [], \json_encode([
            'user' => 7,
            'department' => 2,
            'employee' => 1,
            'followerEmployee' => ['user' => 7, 'department' => 2],
        ]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);
        $this->assertTrue($response);
    }

    /**
     * @return void
     */
    public function testIsFollowerWitAdminLoggedInReturnFalse()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $response = $this->makeJsonRequest('POST', "v2_followers_is_follower", [], \json_encode([
            'user' => 7,
            'department' => 3,
            'employee' => 1,
            'followerEmployee' => ['user' => 7, 'department' => 3],
        ]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);
        $this->assertFalse($response);
    }

    /**
     * @return void
     */
    public function testGetFollowersCountWitAdminLoggedInReturnTwo()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $response = $this->makeJsonRequest('GET', "v2_followers_count", ['employee' => 1], \json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('count', $response);
        $this->assertEquals(2, $response['count']);
    }

    /**
     * @return void
     */
    public function testGetFollowersCountWitUserLoggedInReturnZero()
    {
        $this->configureLoggedInByJWT(LoadUsers::HEAD_USER_EMAIL);

        $response = $this->makeJsonRequest('GET', "v2_followers_count", ['employee' => 8], \json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('count', $response);
        $this->assertEquals(0, $response['count']);
    }

    /**
     * @return void
     */
    public function testGetFollowersListWitAdminLoggedInReturnList()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $response = $this->makeJsonRequest('GET', "v2_followers_list", ['employee' => 1, 'batch' => 1], \json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertListResponse(json_decode($response->getContent(), true));
    }

    /**
     * @return void
     */
    public function testGetFollowersListWitUserLoggedInReturnList()
    {
        $this->configureLoggedInByJWT(LoadUsers::HEAD_USER_EMAIL);

        $response = $this->makeJsonRequest('GET', "v2_followers_list", ['employee' => 1, 'batch' => 1], \json_encode([]));

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertListResponse(json_decode($response->getContent(), true));
    }

    /**
     * @return void
     */
    public function testUnFollowWithUserReturnTrue()
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_NAME);

        $actualEvent = null;
        $eventListenerMock = function (FollowersEvent $event) use (&$actualEvent) {
            $actualEvent = $event;
        };
        $this->container->get('event_dispatcher')
            ->addListener(FollowersEvent::ON_UNFOLLOW, $eventListenerMock);

        $response = $this->makeJsonRequest('POST', "v2_followers_unfollow", [], \json_encode([
            'user' => 2,
            'department' => 3,
            'employee' => 1,
            'unFollowerEmployee' => ['user' => 2, 'department' => 3],
        ]));

        $this->assertEquals(200, $response->getStatusCode());
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $response);
        $this->assertEquals(true, $response['success']);
        $this->assertTrue($response['success']);

        $followedEmployee = $this->entityManager->getRepository(PlusEmployee::class)->findOneBy(['id' => 1]);
        /** @var PlusEmployeeFollower[] $followers */
        $followers = $followedEmployee->getFollowers();
        $this->assertCount(1, $followers);

        $this->normalizeFollowers();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @return FollowersControllerTest
     */
    public function normalizeFollowers(): FollowersControllerTest
    {
        $followerEmployee = $this->entityManager->getRepository(PlusEmployee::class)->findOneBy(['id' => 1]);
        $followers = $this->entityManager->getRepository(PlusEmployeeFollower::class)->findBy(['employee' => $followerEmployee]);

        foreach ($followers as $follower) {
            $this->entityManager->remove($follower);
        }

        $this->entityManager->flush();

        return $this;
    }

    /**
     * @param array $response
     * @return FollowersControllerTest
     */
    private function assertListResponse(array $response): FollowersControllerTest
    {
        $this->assertArrayHasKey('followersList', $response);
        $this->assertArrayHasKey('hasNext', $response);
        $this->assertArrayHasKey(0, $response['followersList']);
        $this->assertArrayHasKey(1, $response['followersList']);
        $this->assertArrayHasKey('id', $response['followersList'][0]);
        $this->assertArrayHasKey('user', $response['followersList'][0]);
        $this->assertArrayHasKey('id', $response['followersList'][1]);
        $this->assertArrayHasKey('user', $response['followersList'][1]);
        $this->assertArrayHasKey('id', $response['followersList'][0]['user']);
        $this->assertArrayHasKey('first_name', $response['followersList'][0]['user']);
        $this->assertArrayHasKey('last_name', $response['followersList'][0]['user']);
        $this->assertArrayHasKey('id', $response['followersList'][1]['user']);
        $this->assertArrayHasKey('first_name', $response['followersList'][1]['user']);
        $this->assertArrayHasKey('last_name', $response['followersList'][1]['user']);
        $this->assertEquals(false, $response['hasNext']);
        $this->assertFalse($response['hasNext']);
        $this->assertEquals(3, $response['followersList'][0]['id']);
        $this->assertEquals(2, $response['followersList'][0]['user']['id']);
        $this->assertEquals('second', $response['followersList'][0]['user']['first_name']);
        $this->assertEquals('second', $response['followersList'][0]['user']['last_name']);
        $this->assertEquals(8, $response['followersList'][1]['id']);
        $this->assertEquals(7, $response['followersList'][1]['user']['id']);
        $this->assertEquals('multiDepartment', $response['followersList'][1]['user']['first_name']);
        $this->assertEquals('multiDepartment', $response['followersList'][1]['user']['last_name']);

        return $this;
    }
}
