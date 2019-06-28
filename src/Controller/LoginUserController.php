<?php
/**
 * Created by anonymous
 * Date: 20/01/18
 * Time: 13:20
 */

namespace App\Controller;

use App\Containers\User\NullUser;
use App\Containers\User\UserRequest;
use App\Entity\User;
use App\Services\User\UserAuthenticator;
use App\Services\User\UserCreator;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class LoginUserController
 */
class LoginUserController extends AbstractFOSRestController implements ApiNoAuthInterface
{
    /**
     * @param UserRequest             $request
     * @param ConstraintViolationList $validationErrors
     * @param UserCreator             $creator
     *
     * @return User
     *
     * @ParamConverter("request",
     *     class="App\Containers\User\UserRequest",
     *     converter="fos_rest.request_body",
     *     options={
     *      "deserializationContext"={"groups"={"create"}},
     *      "validate"={"groups"={"Default"}}
     *     }
     * )
     *
     * @Rest\Post("/no-auth/v1.0/user")
     *
     * @Rest\View(serializerGroups={"create"}, statusCode=200)
     *
     * @throws \Exception
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns created user",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class, groups={"create"})
     *     )
     * )
     * @SWG\Tag(name="no-authentication"),
     * @SWG\Parameter(
     *     name="request",
     *     in="body",
     *     type="string",
     *     description="The field used to request user creation",
     *     allowEmptyValue=false,
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=UserRequest::class, groups={"create"})
     *     )
     * )
     */
    public function createAction(UserRequest $request, ConstraintViolationList $validationErrors, UserCreator $creator)
    {
        if (count($validationErrors) > 0) {
            foreach ($validationErrors->getIterator() as $item) {
                throw new HttpException(400, $item->getMessage());
            }
        }

        return $creator->createByRequest($request);
    }

    /**
     * @param string            $email
     * @param string            $password
     * @param UserAuthenticator $authenticator
     *
     * @return User
     *
     * @Rest\Get("/no-auth/v1.0/user&email={email}&password={password}", requirements={"password" = ".*", "email" = ".*"})
     *
     * @Rest\View(serializerGroups={"log-in"}, statusCode=200)
     *
     * @SWG\Tag(name="no-authentication")
     * @SWG\Response(
     *     response=200,
     *     description="Returns created user",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class, groups={"log-in"})
     *     )
     * )
     *
     * @throws \Exception
     */
    public function getUserByEmailAndPasswordAction(string $email, string $password, UserAuthenticator $authenticator): User
    {
        return $authenticator->authenticateByEmailAndPassword($email, trim($password));
    }

    /**
     * @param string            $fbId
     * @param UserAuthenticator $authenticator
     *
     * @return User|NullUser
     *
     * @Rest\Get("/no-auth/v1.0/user?facebookId={fbId}", requirements={"fbId" = "\d+"})
     *
     * @Rest\View(serializerGroups={"log-in"}, statusCode=200)
     *
     * @SWG\Tag(name="no-authentication")
     * @SWG\Response(
     *     response=200,
     *     description="Returns created user",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=User::class, groups={"log-in"})
     *     ),
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=NullUser::class, groups={"log-in"})
     *     )
     * )
     *
     * @throws \Exception
     */
    public function getUserByFbIdAndFbAuthTokenAction(string $fbId, UserAuthenticator $authenticator)
    {
        try {
            return $authenticator->authenticateByFbId($fbId);
        } catch (HttpException $e) {
            return new NullUser();
        }
    }
}
