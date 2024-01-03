<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Interfaces\Users\AuthServiceInterface;
use Exception;

class AuthController extends Controller
{

    protected $authServiceInterface;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authServiceInterface
    ) {
        $this->middleware('apiJwt:api', ['except' => ['login']]);
        $this->authServiceInterface = $authServiceInterface;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $canUserLogin = $this->authServiceInterface->canUserLogin($validated);

            if (!$canUserLogin) {
                throw new Exception('Email or Password not Found!', 406);
            }


            $token = $this->authServiceInterface->userLogin($validated);
            return response()->json($token, 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
