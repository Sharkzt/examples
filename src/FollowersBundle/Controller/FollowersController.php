<?php

namespace FollowersBundle\Controller;

use AppBundle\Entity\PlusEmployeeFollower;
use AppBundle\Entity\PlusEmployee;
use FollowersBundle\Containers\CountResponse;
use FollowersBundle\Containers\FollowRequest;
use FollowersBundle\Containers\IsFollowerRequest;
use FollowersBundle\Containers\SuccessResponse;
use FollowersBundle\Containers\UnFollowRequest;
use FollowersBundle\Event\FollowersEvent;
use FollowersBundle\Repository\EmployeeFollowersRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View as View;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class FollowersController
 *
 * @Security("has_role('ROLE_ADMINISTRATOR') || has_role('ROLE_PUSHER') || has_role('ROLE_SUBSCRIBER') || has_role('ROLE_EDITOR')")
 * @deprecated use UserManagement::UserController instead
 */
class FollowersController extends Controller
{
    /**
     * @ParamConverter("plusUser", converter="doctrine.orm", class="AppBundle\Entity\PlusUser", options={
     *    "repository_method" = "findOneBy",
     *    "mapping": {"user" = "id"}
     * })
     * @ParamConverter("department", converter="doctrine.orm", class="AppBundle\Entity\PlusDepartment", options={
     *    "repository_method" = "findOneBy",
     *    "mapping": {"department" = "id"}
     * })
     * @ParamConverter("employee", converter="doctrine.orm", class="AppBundle\Entity\PlusEmployee", options={
     *    "repository_method" = "findOneBy",
     *    "mapping": {"employee" = "id"}
     * })
     * @ParamConverter("ownerEmployee", converter="doctrine.orm", class="AppBundle\Entity\PlusEmployee", options={
     *    "repository_method" = "findByUserAndDepartment",
     *    "mapping": {"plusUser" = "user", "department" = "department"},
     *    "map_method_signature" = true
     * })
     *
     * @View(serializerGroups={"success"})
     *
     * @param PlusEmployee $employee
     * @param PlusEmployee $ownerEmployee
     *
     * @return SuccessResponse
     */
    public function isOwnerAction(
        PlusEmployee $employee,
        PlusEmployee $ownerEmployee
    ) {
        return $this->get('followers.success_response')->setSuccess($employee->getId() === $ownerEmployee->getId());
    }

    /**
     * @ParamConverter("request",
     *     class="FollowersBundle\Containers\FollowRequest",
     *     converter="fos_rest.request_body",
     *     options={
     *      "deserializationContext"={"groups"={"follow"}},
     *      "validate"={"groups"={"Default"}}
     *     }
     * )
     *
     * @View(serializerGroups={"success"})
     *
     * @param FollowRequest           $request
     * @param ConstraintViolationList $validationErrors
     * @return SuccessResponse|ConstraintViolationList
     */
    public function followAction(FollowRequest $request, ConstraintViolationList $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            return $validationErrors;
        }

        $event = (new FollowersEvent())
            ->setEmployee($request->getFollowerEmployee())
            ->setFollowedEmployee($request->getEmployee());

        $this->get('event_dispatcher')->dispatch(FollowersEvent::ON_FOLLOW, $event);
        $this->get('event_dispatcher')->dispatch(FollowersEvent::POST_FOLLOW, $event);

        return $this->get('followers.success_response')->setSuccess(true);
    }

    /**
     * @ParamConverter("request",
     *     class="FollowersBundle\Containers\UnFollowRequest",
     *     converter="fos_rest.request_body",
     *     options={
     *      "deserializationContext"={"groups"={"unFollow"}},
     *      "validate"={"groups"={"Default"}}
     *     }
     * )
     *
     * @View(serializerGroups={"success"})
     *
     * @param UnFollowRequest         $request
     * @param ConstraintViolationList $validationErrors
     * @return SuccessResponse|ConstraintViolationList
     */
    public function unFollowAction(UnFollowRequest $request, ConstraintViolationList $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            return $validationErrors;
        }
        /** @var EmployeeFollowersRepository $repo */
        $repo = $this->get("doctrine.orm.entity_manager")->getRepository(PlusEmployeeFollower::class);
        /** @var PlusEmployeeFollower $follower */
        $follower = $repo->findOneBy(['employee' => $request->getEmployee(), 'follower' => $request->getUnFollowerEmployee()]);

        if (!$follower) {
            throw new UnprocessableEntityHttpException(sprintf("Follower for passed parameters doesn't exist"));
        }

        $event = (new FollowersEvent())
            ->setPlusFollowers($follower);

        $this->get('event_dispatcher')->dispatch(FollowersEvent::ON_UNFOLLOW, $event);

        return $this->get('followers.success_response')->setSuccess(true);
    }

    /**
     * @ParamConverter("request",
     *     class="FollowersBundle\Containers\IsFollowerRequest",
     *     converter="fos_rest.request_body",
     *     options={
     *      "deserializationContext"={"groups"={"isFollower"}},
     *      "validate"={"groups"={"Default"}}
     *     }
     * )
     *
     * @View(serializerGroups={"success"})
     *
     * @param IsFollowerRequest       $request
     * @param ConstraintViolationList $validationErrors
     * @return SuccessResponse|ConstraintViolationList
     */
    public function isFollowerAction(IsFollowerRequest $request, ConstraintViolationList $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            return $validationErrors;
        }

        return $this->get('followers.followers_bound')->isFollower($request->getEmployee(), $request->getFollowerEmployee());
    }

    /**
     * @ParamConverter("employee", converter="doctrine.orm", class="AppBundle\Entity\PlusEmployee", options={
     *    "repository_method" = "findOneBy",
     *    "mapping": {"employee" = "id"}
     * })
     *
     * @View(serializerGroups={"count"})
     *
     * @param PlusEmployee $employee
     *
     * @return CountResponse
     */
    public function followersCountAction(PlusEmployee $employee)
    {
        return $this->get("followers.count.response")->setCount($this->get("followers.followers_counter")->getFollowersCount($employee));
    }

    /**
     * @ParamConverter("employee", converter="doctrine.orm", class="AppBundle\Entity\PlusEmployee", options={
     *    "repository_method" = "findOneBy",
     *    "mapping": {"employee" = "id"}
     * })
     *
     * @View(
     *     serializerGroups={"followersList"},
     *     statusCode=200,
     *     )
     *
     * @param PlusEmployee $employee
     * @param int          $batch
     *
     * @return CountResponse
     */
    public function getFollowersListAction(PlusEmployee $employee, int $batch)
    {
        $list = $this->get("followers.list");

        return $this->get("followers.list.response")
            ->setFollowersList($list->getList($employee, $batch))
            ->setHasNext($list->hasNext($employee, $batch));
    }
}
