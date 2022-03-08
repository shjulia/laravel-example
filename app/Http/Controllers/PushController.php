<?php

namespace App\Http\Controllers;

use App\Entities\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class PushController
 * @package App\Http\Controllers
 */
class PushController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $playerId = $request->userId;
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            if (!$user->players()->where(['player_id' => $playerId])->exists()) {
                $user->players()->create([
                    'player_id' => $playerId
                ]);
            }
        }
        return response()->json(['success' => 1], Response::HTTP_OK);
    }
}
