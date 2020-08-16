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
     * @OA\SecurityScheme(
     *     securityScheme="passport",
     *     type="http",
     *     scheme="bearer",
     * )
     */

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
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Signup",
     *     description="Signup",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "email", "password", "password_confirmation"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     format="string",
     *                     description="name",
     *                     example="test001",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="string",
     *                     description="email",
     *                     example="test001@test.com",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="password",
     *                     example="123456",
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                     description="password confirmation",
     *                     example="123456",
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully created user!",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Failed.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Failed to create user!",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The email field is required.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     description="Login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     format="string",
     *                     description="email",
     *                     example="test001@test.com",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="password",
     *                     example="123456",
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(property="id", type="integer" ,format="int64", example=1),
     *                     @OA\Property(property="name", type="string", format="string", example="test001"),
     *                     @OA\Property(property="email", type="string", format="string", example="test001@test.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     *                     @OA\Property(property="token", type="string", format="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJ..."),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Unauthorized",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The email field is required.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
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
     *
     * @OA\Get(
     *     path="/api/logout",
     *     summary="Logout",
     *     description="Logout",
     *     tags={"Auth"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully logged out",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Unauthorized",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
