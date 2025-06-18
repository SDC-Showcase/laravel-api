<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    use ApiResponses;

    /**
     * Handle the incoming request to test API functionality.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function apitest(Request $request) {
        $user = User::find(1);
        if ($user) {
            return $this->success(['message' => 'API is working', 'user' => $user]);
        } else {
            return $this->error('User not found', 404);
        }
    }

}
