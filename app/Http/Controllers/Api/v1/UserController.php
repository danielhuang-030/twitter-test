<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ApiResponseCode;
use App\Http\Requests\Api\v1\User\PostsRequest;
use App\Http\Resources\Api\v1\Post\PostResource;
use App\Http\Resources\Api\v1\User\UserResource;
use App\Models\User;
use App\Params\PostParam;
use App\Services\PostService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * @var User
     *
     * @OA\Schema(
     *     schema="UserResponse",
     *     type="object",
     *     title="User Response",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          format="int64",
     *          example=1,
     *     ),
     *     @OA\Property(
     *          property="name",
     *          type="string",
     *          format="string",
     *          example="test001",
     *     ),
     *     @OA\Property(
     *          property="email",
     *          type="string",
     *          format="string",
     *          example="test001@test.com",
     *     ),
     *     @OA\Property(
     *          property="created_at",
     *          type="string",
     *          format="string",
     *          example="2022-03-10 17:45:16",
     *     ),
     *     @OA\Property(
     *          property="updated_at",
     *          type="string",
     *          format="string",
     *          example="2022-03-10 17:45:16",
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="UserNotExistResponse",
     *     type="object",
     *     title="User Not Exist Response",
     *
     *     @OA\Property(
     *          property="code",
     *          type="string",
     *          example="500001",
     *     ),
     *     @OA\Property(
     *          property="message",
     *          type="string",
     *          example="User does not exist.",
     *     ),
     *     @OA\Property(
     *          property="data",
     *          type="object",
     *     ),
     * ),
     */
    protected $user;

    public function __construct(protected UserService $userService, protected PostService $postService)
    {
        parent::__construct();

        // get user
        $this->middleware(function ($request, $next) {
            $id = (int) $request->route('id');
            $this->user = (empty($id) ? auth()->user() : $this->userService->getUser($id));
            if (empty($this->user)) {
                return $this->responseFail(code: ApiResponseCode::ERROR_USER_NOT_EXIST->value);
            }

            return $next($request);
        });
    }

    /**
     * info.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}/info",
     *     summary="User Info",
     *     description="User info",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *
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
     *                               ref="#/components/schemas/UserResponse",
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="User does not exist.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserNotExistResponse")
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
     * @param int     $id
     */
    public function info(Request $request, $id)
    {
        return $this->responseSuccess(data: [
            'user' => UserResource::make($this->user),
        ]);
    }

    /**
     * following.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}/following",
     *     summary="User Following",
     *     description="User following list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
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
     *                               property="following",
     *                               type="array",
     *
     *                               @OA\Items(ref="#/components/schemas/UserResponse")
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="User does not exist.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserNotExistResponse")
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
     * @param int     $id
     */
    public function following(Request $request, $id)
    {
        $following = $this->user->load([
            'following',
        ])->following;

        return $this->responseSuccess(data: [
            'following' => UserResource::collection($following),
        ]);
    }

    /**
     * followers.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}/followers",
     *     summary="User followers",
     *     description="User follower list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
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
     *                               property="followers",
     *                               type="array",
     *
     *                               @OA\Items(ref="#/components/schemas/UserResponse")
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="User does not exist.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserNotExistResponse")
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
     * @param int     $id
     */
    public function followers(Request $request, $id)
    {
        $followers = $this->user->load([
            'followers',
        ])->followers;

        return $this->responseSuccess(data: [
            'followers' => UserResource::collection($followers),
        ]);
    }

    /**
     * posts.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}/posts",
     *     summary="User Posts",
     *     description="User post list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="page",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="page size",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=10,
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="sort by",
     *
     *         @OA\Schema(
     *             type="string",
     *             example="updated_at",
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="is_desc",
     *         in="query",
     *         required=false,
     *         description="is sort by desc",
     *
     *         @OA\Schema(
     *             type="integer",
     *             example=1,
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
     *                     type="object",
     *                     allOf={
     *                         @OA\Schema(
     *
     *                             @OA\Property(
     *                                  property="code",
     *                                  type="string",
     *                                  example="000000",
     *                             ),
     *                             @OA\Property(
     *                                  property="message",
     *                                  type="string",
     *                                  example="Success.",
     *                             ),
     *                             @OA\Property(
     *                                  property="data",
     *                                  type="object",
     *                                  @OA\Property(
     *                                       property="pagination",
     *                                       type="object",
     *                                       @OA\Property(
     *                                            property="page",
     *                                            type="number",
     *                                            example=1,
     *                                       ),
     *                                       @OA\Property(
     *                                            property="per_page",
     *                                            type="number",
     *                                            example=20,
     *                                       ),
     *                                       @OA\Property(
     *                                            property="total",
     *                                            type="number",
     *                                            example=5,
     *                                       ),
     *                                  ),
     *                                  @OA\Property(
     *                                       property="data",
     *                                       type="array",
     *
     *                                       @OA\Items(ref="#/components/schemas/PostResponse"),
     *                                  ),
     *                             ),
     *                         ),
     *                     },
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="User does not exist.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserNotExistResponse")
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
     * @param PostsRequest $request
     * @param int          $id
     */
    public function posts(PostsRequest $request, $id)
    {
        $paginator = $this->postService->getPosts(
            (new PostParam($request))->setUserId($id)
                ->setWiths([
                    'user',
                ])
        );

        return $this->responseSuccessWithPagination(
            paginator: $paginator,
            data: PostResource::collection($paginator)
        );
    }

    /**
     * liked posts.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}/liked_posts",
     *     summary="User Liked Posts",
     *     description="User liked post list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
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
     *                               property="posts",
     *                               type="array",
     *
     *                               @OA\Items(ref="#/components/schemas/PostResponse"),
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="User does not exist.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UserNotExistResponse")
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
     * @param int     $id
     */
    public function likedPosts(Request $request, $id)
    {
        $likePosts = $this->user->load([
            'likePosts' => function ($query) {
                $query->orderBy('updated_at', 'desc');
            },
        ])->likePosts;

        return $this->responseSuccess(
            data: [
                'posts' => PostResource::collection($likePosts),
            ]
        );
    }
}
