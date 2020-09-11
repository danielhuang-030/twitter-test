<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\PostsRequest;
use App\Params\PostParam;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="UserResponse",
     *     type="object",
     *     title="User Response",
     *     @OA\Property(property="id", type="integer" ,format="int64", example=1),
     *     @OA\Property(property="name", type="string", format="string", example="test001"),
     *     @OA\Property(property="email", type="string", format="string", example="test001@test.com"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     * )
     */

    /**
     * UserService.
     *
     * @var UserService
     */
    protected $userService;

    /**
     * User.
     *
     * @var User
     */
    protected $user;

    /**
     * construct.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        // get user
        $this->middleware(function ($request, $next) {
            $id = $request->route('id', 0);
            $this->user = (empty($id) ? Auth::user() : $this->userService->getUser($id));
            if (null === $this->user) {
                return response()->json([
                    'message' => 'error',
                ], Response::HTTP_BAD_REQUEST);
            }

            return $next($request);
        });
    }

    /**
     * info.
     *
     * @OA\Get(
     *     path="/api/users/{id}/info",
     *     summary="User Info",
     *     description="User info",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         @OA\JsonContent(ref="#/components/schemas/UserResponse")
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
     *                         example="error",
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
     * @param int     $id
     */
    public function info(Request $request, $id)
    {
        return response()->json($this->user);
    }

    /**
     * following.
     *
     * @OA\Get(
     *     path="/api/users/{id}/following",
     *     summary="User Following",
     *     description="User following list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/UserResponse"),
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
     *                         example="error",
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
     * @param int     $id
     */
    public function following(Request $request, $id)
    {
        return response()->json($this->user->following);
    }

    /**
     * followers.
     *
     * @OA\Get(
     *     path="/api/users/{id}/followers",
     *     summary="User followers",
     *     description="User follower list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/UserResponse"),
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
     *                         example="error",
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
     * @param int     $id
     */
    public function followers(Request $request, $id)
    {
        return response()->json($this->user->followers);
    }

    /**
     * posts.
     *
     * @OA\Get(
     *     path="/api/users/{id}/posts",
     *     summary="User Posts",
     *     description="User post list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="page",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         required=false,
     *         description="page size",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=10,
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     allOf={
     *                         @OA\Schema(
     *                             @OA\Property(
     *                                 property="data",
     *                                 type="array",
     *                                 @OA\Items(ref="#/components/schemas/PostResponse"),
     *                             ),
     *                         ),
     *                     },
     *                     @OA\Property(
     *                         property="page",
     *                         type="integer",
     *                         format="int64",
     *                         description="page",
     *                         example=1,
     *                     ),
     *                     @OA\Property(
     *                         property="page_size",
     *                         type="integer",
     *                         format="int64",
     *                         description="page size",
     *                         example=10,
     *                     ),
     *                     @OA\Property(
     *                         property="total",
     *                         type="integer",
     *                         format="int64",
     *                         description="total",
     *                         example=1,
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
     *                         example="error",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *     @OA\Response(
     *         response="422",
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
     *                         example="The number of pages must be at least 1.",
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
     * @param PostsRequest $request
     * @param int          $id
     */
    public function posts(PostsRequest $request, $id)
    {
        $paginator = $this->userService->getPosts((new PostParam($request))->setUserId($id));

        return response()->json([
            'data'      => $paginator->getCollection(),
            'page'      => $paginator->currentPage(),
            'page_size' => $paginator->perPage(),
            'total'     => $paginator->total(),
        ]);
    }

    /**
     * liked posts.
     *
     * @OA\Get(
     *     path="/api/users/{id}/liked_posts",
     *     summary="User Liked Posts",
     *     description="User liked post list",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1,
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successfully.",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/PostResponse"),
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
     *                         example="error",
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
     * @param int     $id
     */
    public function likedPosts(Request $request, $id)
    {
        return response()->json($this->user->load(['likePosts' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->likePosts);
    }
}
