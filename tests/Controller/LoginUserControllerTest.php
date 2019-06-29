<?php
/**
 * Created by anonymous
 * Date: 11/02/18
 * Time: 12:03
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\TestCases\AbstractFunctionalTestCase;

/**
 * Class LoginUserControllerTest
 */
class LoginUserControllerTest extends AbstractFunctionalTestCase
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testCreateUserWithEmailAndPasswordReturnUser()
    {
        $response = $this->makeJsonRequest(
            'POST',
            "app_loginuser_create",
            [],
            \json_encode([
                'email' => 'test4@test.com',
                'password' => 'somepassword',
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getContent(), true);


        $this->assertArrayHasKey('email', $response);
        $this->assertEquals('test4@test.com', $response['email']);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testCreateUserWithFbCredentialsAndFirstNameAndLastNameReturnUser()
    {
        $response = $this->makeJsonRequest(
            'POST',
            "app_loginuser_create",
            [],
            \json_encode([
                'firstName' => 'Test First Name 5',
                'lastName' => 'Test Last Name 5',
                'email' => 'test5@test.com',
                'fbAuthToken' => '33b7e38f1ed06fc9df4a2be88f2623647df5ed21d62737921138a469c325f6668262bb2df0d5e95db38d310bf4d6dc5e448b023c784834d99b3e94b3c30fa6b3',
                'fbId' => '12323675886796792423435896809',
                'sex' => false,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $response = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('fbId', $response);
        $this->assertArrayHasKey('sex', $response);
        $this->assertEquals('test5@test.com', $response['email']);
        $this->assertEquals('Test First Name 5', $response['firstName']);
        $this->assertEquals('Test Last Name 5', $response['lastName']);
        $this->assertEquals('12323675886796792423435896809', $response['fbId']);
        $this->assertEquals(false, $response['sex']);
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testGetUserByEmailAndPasswordReturnUser()
    {
        $response = $this->makeJsonRequest(
            'GET',
            "app_loginuser_getuserbyemailandpassword",
            [
                'email' => 'test4@test.com',
                'password' => 'somepassword',
            ],
            \json_encode([])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    public function testGetUserByFbCredentialsReturnUser()
    {
        $response = $this->makeJsonRequest(
            'GET',
            "app_loginuser_getuserbyfbidandfbauthtoken",
            [
                'fbId' => '12323675886796792423435896809',
            ],
            \json_encode([])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $this->normalizeDB();
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @return LoginUserControllerTest
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function normalizeDB(): LoginUserControllerTest
    {
        $user1 = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'test4@test.com']);
        $user2 = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => 'test5@test.com']);

        $this->getEntityManager()->remove($user1);
        $this->getEntityManager()->remove($user2);

        $this->getEntityManager()->flush();

        return $this;
    }
}
