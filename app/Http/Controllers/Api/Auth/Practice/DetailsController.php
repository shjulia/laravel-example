<?php

namespace App\Http\Controllers\Api\Auth\Practice;

use App\Entities\User\Role;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Practice\Details\BaseDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\SecondaryDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\TeamMemberRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\DetailsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DetailsController
 * @package App\Http\Controllers\Api\Auth\Practice
 */
class DetailsController extends Controller
{
    /**
     * @var DetailsService
     */
    private $detailsService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * DetailsController constructor.
     * @param DetailsService $detailsService
     * @param UserRepository $userRepository
     */
    public function __construct(DetailsService $detailsService, UserRepository $userRepository)
    {
        $this->middleware(['auth:api', 'can:practice-details']);
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getUserWithPractice(Auth::user()->id);
                return $next($request);
            } catch (\DomainException $e) {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
            }
        });
        $this->detailsService = $detailsService;
        $this->userRepository = $userRepository;
    }

    /**
     * @SWG\Post(
     *     path="/practice/details/save-photo",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(name="file", in="formData", required=true, type="file"),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider upload driver license",
     *         @SWG\Schema(
     *              @SWG\Property(property="path", type="string"),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     * @param PhotoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePhoto(PhotoRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $path = $this->detailsService->savePhoto($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([$path], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/details/save-practice-details",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/BaseDetailsRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice base details save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
     *              ),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param BaseDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveBaseDetails(BaseDetailsRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->saveDetails($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/details/secondary",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(
     *     name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/SecondaryDetailsRequest")
     * ),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice secondary details save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
     *              ),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param SecondaryDetailsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSecondaryDetails(SecondaryDetailsRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->saveSecondaryDetails($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Get(
     *     path="/practice/details/team",
     *     tags={"PracticeDetails"},
     *     @SWG\Response(
     *         response="200",
     *         description="Practice team",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="roles",
     *                  type="array",
     *                  @SWG\Items(
     *                      @SWG\Property(property="user_role", type="string")
     *                  )
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
     *              ),
     *              @SWG\Property(
     *                  property="users",
     *                  type="array",
     *                  @SWG\Items(
     *                      ref="#/definitions/UserBase"
     *                  )
     *              ),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function team()
    {
        $user = $this->user;
        $users = $user->practice->users;
        $roles = Role::practiceRoles();
        return response()->json(['user' => $user, 'users' => $users, 'roles' => $roles], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/details/team",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/TeamMemberRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Add new member to practice",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
     *              ),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param TeamMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamSave(TeamMemberRequest $request)
    {
        $user = $this->user;
        try {
            $user = $this->detailsService->saveTeamMember($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Delete(
     *     path="/practice/details/team",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(name="member", in="formData", required=true, type="integer", description="ID of team member"),
     *     @SWG\Response(
     *         response="200",
     *         description="Delete team member",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMember(Request $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->deleteTeamMember($request->member, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['success' => true], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/details/save-billing",
     *     tags={"PracticeDetails"},
     *     @SWG\Parameter(name="token", in="formData", required=true, type="string",
     *     description="Generated stripe token"),
     *     @SWG\Response(
     *         response="200",
     *         description="Save practice billing info",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function billingSave(Request $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->billingSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['success' => true], Response::HTTP_OK);
    }
}
