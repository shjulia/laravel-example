<?php

namespace App\Http\Controllers\Api;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *     @SWG\GET(
     *     path="/user-data",
     *     tags={"User"},
     *     @SWG\Response(
     *         response="200",
     *         description="Get all user data",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object"
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserData()
    {
        $user = Auth::user();
        $user = $this->userRepository->getUserWithFullData($user);
        return response()->json(['user' => $user], Response::HTTP_OK);
    }
}
