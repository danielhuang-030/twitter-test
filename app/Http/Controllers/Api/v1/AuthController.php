<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ApiResponseCode;
use App\Http\Requests\Api\v1\Auth\AuthLoginRequest;
use App\Http\Requests\Api\v1\Auth\AuthSignupRequest;
use App\Http\Resources\Api\v1\User\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="passport",
     *     type="http",
     *     scheme="bearer",
     * ),
     *
     * @OA\Schema(
     *     schema="ValidationErrorResponse",
     *     type="object",
     *     title="Validation Error Response",
     *
     *     @OA\Property(
     *          property="code",
     *          type="string",
     *          example="999001",
     *     ),
     *     @OA\Property(
     *          property="message",
     *          type="string",
     *          example="The selected id is invalid.",
     *     ),
     *     @OA\Property(
     *          property="data",
     *          type="object",
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="UnauthorizedResponse",
     *     type="object",
     *     title="Unauthorized Response",
     *
     *     @OA\Property(
     *          property="code",
     *          type="string",
     *          example="999002",
     *     ),
     *     @OA\Property(
     *          property="message",
     *          type="string",
     *          example="Unauthorized.",
     *     ),
     *     @OA\Property(
     *          property="data",
     *          type="object",
     *     ),
     * ),
     */
    public function __construct(protected AuthService $service, protected UserService $userService)
    {
        parent::__construct();
    }

    /**
     * signup.
     *
     * @OA\Post(
     *     path="/api/v1/signup",
     *     summary="Signup",
     *     description="Signup",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 required={"name", "email", "password", "password_confirmation"},
     *
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
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                          property="code",
     *                          type="string",
     *                          example="000000",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="User created successfully!",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                          property="code",
     *                          type="string",
     *                          example="500002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="User add failed.",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"),
     *     ),
     * )
     *
     * @param AuthSignupRequest $request
     */
    public function signup(AuthSignupRequest $request)
    {
        $user = $this->userService->create($request->validated());
        if (empty($user)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_USER_ADD->value);
        }

        return $this->responseSuccess(message: 'User created successfully!');
    }

    /**
     * login.
     *
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login",
     *     description="Login",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 required={"email", "password"},
     *
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
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                          property="code",
     *                          type="string",
     *                          example="000000",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Success.",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          @OA\Property(
     *                               property="user",
     *                               type="object",
     *                               @OA\Property(
     *                                    property="id",
     *                                    type="number",
     *                                    example=1,
     *                               ),
     *                               @OA\Property(
     *                                    property="name",
     *                                    type="string",
     *                                    example="test001",
     *                               ),
     *                               @OA\Property(
     *                                    property="email",
     *                                    type="string",
     *                                    example="test001@test.com",
     *                               ),
     *                               @OA\Property(
     *                                    property="created_at",
     *                                    type="string",
     *                                    example="2022-03-10 17:45:16",
     *                               ),
     *                               @OA\Property(
     *                                    property="updated_at",
     *                                    type="string",
     *                                    example="2022-03-10 17:45:16",
     *                               ),
     *                          ),
     *                          @OA\Property(
     *                               property="token",
     *                               type="string",
     *                               example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse"),
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"),
     *     ),
     * )
     *
     * @param AuthLoginRequest $request
     */
    public function login(AuthLoginRequest $request)
    {
        $user = $this->service->attempt($request->validated());
        if (empty($user)) {
            return $this->responseFail(
                code: ApiResponseCode::ERROR_UNAUTHORIZED->value,
                message: 'Unauthorized',
                httpStatusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->responseSuccess(array_merge([
            'user' => UserResource::make($user),
        ], [
            'token' => $user->token(),
        ]));
    }

    /**
     * Logout user (Revoke the token).
     *
     * @OA\Get(
     *     path="/api/v1/logout",
     *     summary="Logout",
     *     description="Logout",
     *     tags={"Auth"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                          property="code",
     *                          type="string",
     *                          example="000000",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Successfully logged out!",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse"),
     *     ),
     * )
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return $this->responseSuccess(message: 'Successfully logged out!');
    }
}
