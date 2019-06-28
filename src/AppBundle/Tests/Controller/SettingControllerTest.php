<?php

namespace AppBundle\Tests\Controller;

use AppBundle\DataFixtures\TestData\LoadWpUsers;
use AppBundle\Entity\PlusSetting;
use AppBundle\Entity\PlusUser;
use AppBundle\TestHelpers\TestCases\FunctionalTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingControllerTest
 *
 * @group main
 */
class SettingControllerTest extends FunctionalTestCase
{
    /**
     * @var PlusUser
     */
    private $loggedInUser;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /**
         * @var PlusUser $user
         */
        $this->loggedInUser = $user = $this->entityManager->getRepository(PlusUser::class)
            ->findOneBy(['email' => LoadUsers::ADMIN_USER_EMAIL]);

        $this->configureLoggedInByJWT($user->getEmail());
    }

    /**
     * @return void
     *
     * @throws MappingException
     */
    public function testUpdateEmailSenderAddressWithStringReturnSuccess(): void
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_EMAIL);
        $response = $this->makeJsonRequest('PATCH', 'v2_update_setting', ['setting' => PlusSetting::EMAIL_SENDER_ADDRESS, 'value' => 'foo@bar.com']);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->entityManager->clear();
        /** @var PlusSetting $setting */
        $setting = $this->entityManager->getRepository(PlusSetting::class)->findOneBy(['name' => PlusSetting::EMAIL_SENDER_ADDRESS]);
        $this->assertEquals('foo@bar.com', $setting->getValue());
    }

    /**
     * @return void
     *
     * @throws MappingException
     */
    public function testUpdateEmailSenderNameWithStringReturnSuccess(): void
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_EMAIL);
        $response = $this->makeJsonRequest('PATCH', 'v2_update_setting', ['setting' => PlusSetting::EMAIL_SENDER_NAME, 'value' => 'foo']);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->entityManager->clear();
        /** @var PlusSetting $setting */
        $setting = $this->entityManager->getRepository(PlusSetting::class)->findOneBy(['name' => PlusSetting::EMAIL_SENDER_NAME]);
        $this->assertEquals('foo', $setting->getValue());
    }

    /**
     * @return void
     *
     * @throws MappingException
     */
    public function testUpdateCompanyNameWithStringReturnSuccess(): void
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_EMAIL);
        $response = $this->makeJsonRequest('PATCH', 'v2_update_setting', ['setting' => PlusSetting::COMPANY_NAME, 'value' => 'foo']);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->entityManager->clear();
        /** @var PlusSetting $setting */
        $setting = $this->entityManager->getRepository(PlusSetting::class)->findOneBy(['name' => PlusSetting::COMPANY_NAME]);
        $this->assertEquals('foo', $setting->getValue());
    }

    /**
     * @return void
     */
    public function testGetAllReturnList(): void
    {
        $this->configureLoggedInByJWT(LoadUsers::ADMIN_USER_EMAIL);
        $response = $this->makeJsonRequest('GET', 'v2_list_settings', []);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $result = \json_decode($response->getContent(), true);

        foreach ($result as $item) {
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('is_translatable', $item);
        }
    }
}
