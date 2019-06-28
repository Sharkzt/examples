<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PlusSetting;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FollowersBundle\Containers\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations AS FOS;
use FOS\RestBundle\Controller\ControllerTrait as FOSControllerTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @Security("has_role('ROLE_ADMIN')")
 *
 * Class SettingController
 */
class SettingController extends Controller
{
    use FOSControllerTrait;

    /**
     * @FOS\QueryParam(name="setting", nullable=false)
     * @FOS\QueryParam(name="value", nullable=false)
     *
     * @ParamConverter("setting", converter="doctrine.orm", class="AppBundle\Entity\PlusSetting", options={
     *    "repository_method" = "getPlusSettingValueByName",
     *    "mapping": {"setting" = "name"},
     *    "map_method_signature" = true
     * })
     *
     * @Rest\View(serializerGroups={"success"})
     *
     * @param PlusSetting $setting
     * @param string      $value
     *
     * @return SuccessResponse
     *
     * @throws ORMException
     */
    public function updateSettingByNameAndValueAction(PlusSetting $setting, string $value)
    {
        /** @var EntityManager $em */
        $em =  $this->get('doctrine.orm.entity_manager');
        $setting->setValue($value);
        $em->merge($setting);
        $em->flush();

        return new SuccessResponse();
    }

    /**
     * @Rest\View(serializerGroups={"settings_list"})
     *
     * @return PlusSetting[]|array
     */
    public function getAllAction()
    {
        /** @var EntityManager $em */
        $em =  $this->get('doctrine.orm.entity_manager');

        return $em->getRepository(PlusSetting::class)->findAll();
    }
}
