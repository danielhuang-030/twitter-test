<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * UserService.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * construct.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * signup.
     *
     * @param SignupRequest $request
     */
    public function signup(SignupRequest $request)
    {
        if (null === $this->userService->create($request->only([
            'name',
            'email',
            'password',
        ]))) {
            return response()->json([
                'message' => 'Failed to create user!',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully created user!',
        ]);
    }

    /**
     * login.
     *
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        $user = $this->userService->attempt($request->only([
            'email',
            'password',
        ]));
        if (null === $user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(array_merge($user->toArray(), [
            'token' => $user->token(),
        ]));
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
