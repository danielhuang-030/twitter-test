<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ApiResponseCode;
use App\Http\Requests\Api\v1\Post\DislikeRequest;
use App\Http\Requests\Api\v1\Post\LikeRequest;
use App\Http\Requests\Api\v1\Post\ShowRequest;
use App\Http\Requests\Api\v1\Post\StoreRequest;
use App\Http\Requests\Api\v1\Post\UpdateRequest;
use App\Http\Resources\Api\v1\Post\PostResource;
use App\Http\Resources\Api\v1\User\UserResource;
use App\Services\PostService;

class PostController extends BaseController
{
    /**
     * @OA\Schema(
     *     schema="PostResponse",
     *     type="object",
     *     title="Post Response",
     *
     *     @OA\Property(
     *          property="id",
     *          type="number",
     *          example=1,
     *     ),
     *     @OA\Property(
     *          property="author",
     *          type="string",
     *          example="test001",
     *     ),
     *     @OA\Property(
     *          property="content",
     *          type="string",
     *          example="test\ntest2",
     *     ),
     *     @OA\Property(
     *          property="created_at",
     *          type="string",
     *          example="2023-01-31 16:47:43",
     *     ),
     *     @OA\Property(
     *          property="updated_at",
     *          type="string",
     *          example="2023-01-31 16:47:43",
     *     ),
     * ),
     *
     * @OA\Schema(
     *     schema="PostNotExistResponse",
     *     type="object",
     *     title="Post Not Exist Response",
     *
     *     @OA\Property(
     *          property="code",
     *          type="string",
     *          example="501001",
     *     ),
     *     @OA\Property(
     *          property="message",
     *          type="string",
     *          example="Post does not exist.",
     *     ),
     *     @OA\Property(
     *          property="data",
     *          type="object",
     *     ),
     * ),
     */
    public function __construct(protected PostService $postService)
    {
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/v1/posts",
     *     summary="Post Store",
     *     description="Post store",
     *     tags={"Post"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 required={"content"},
     *
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     format="string",
     *                     description="content",
     *                     example="test",
     *                 ),
     *             ),
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
     *                               property="post",
     *                               ref="#/components/schemas/PostResponse",
     *                          ),
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
     *                          example="501002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Post add failed.",
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"),
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $post = $this->postService->add($request->validated(), (int) auth()->user()?->id);

        return $this->responseSuccess([
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/v1/posts/{id}",
     *     summary="Post Show",
     *     description="Post show",
     *     tags={"Post"},
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
     *                               property="post",
     *                               ref="#/components/schemas/PostResponse",
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PostNotExistResponse")
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
     * @param int         $id
     * @param ShowRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, int $id)
    {
        $post = $this->postService->find($id);

        return $this->responseSuccess(data: [
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/posts/{id}",
     *     summary="Post Update",
     *     description="Post update",
     *     tags={"Post"},
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
     *     @OA\RequestBody(
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 required={"content"},
     *
     *                 @OA\Property(
     *                     property="content",
     *                     type="string",
     *                     format="string",
     *                     description="content",
     *                     example="test",
     *                 ),
     *             ),
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
     *                               property="post",
     *                               ref="#/components/schemas/PostResponse",
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
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $post = $this->postService->edit($request->validated(), $id, (int) auth()->user()?->id);

        return $this->responseSuccess(data: [
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/v1/posts/{id}",
     *     summary="Post Delete",
     *     description="Post delete",
     *     tags={"Post"},
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
     *                          example="Successfully deleted post!",
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
     *                          example="501004",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Post delete failed.",
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->postService->del($id, (int) auth()->user()?->id)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_DEL->value);
        }

        return $this->responseSuccess(message: 'Successfully deleted post!');
    }

    /**
     * like.
     *
     * @OA\Patch(
     *     path="/api/v1/posts/{id}/like",
     *     summary="Post Like",
     *     description="Post like",
     *     tags={"Post"},
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
     *                          example="Successfully liked post!",
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
     *                          example="501005",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Post like failed.",
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"),
     *     ),
     * )
     *
     * @param LikeRequest $request
     * @param int         $id
     */
    public function like(LikeRequest $request, $id)
    {
        if (!$this->postService->like($id, (int) auth()->user()?->id)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_LIKE->value);
        }

        return $this->responseSuccess(message: 'Successfully liked post!');
    }

    /**
     * dislike.
     *
     * @OA\Delete(
     *     path="/api/v1/posts/{id}/like",
     *     summary="Post Dislike",
     *     description="Post dislike",
     *     tags={"Post"},
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
     *                          example="Successfully disliked post!",
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
     *                          example="501006",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Post dislike failed.",
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse"),
     *     ),
     * )
     *
     * @param DislikeRequest $request
     * @param int            $id
     */
    public function dislike(DislikeRequest $request, $id)
    {
        if (!$this->postService->dislike($id, (int) auth()->user()?->id)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_DISLIKE->value);
        }

        return $this->responseSuccess(message: 'Successfully disliked post!');
    }

    /**
     * liked users.
     *
     * @OA\Get(
     *     path="/api/v1/posts/{id}/liked_users",
     *     summary="Post Liked Users",
     *     description="Post liked user list",
     *     tags={"Post"},
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
     *                               property="users",
     *                               type="array",
     *
     *                               @OA\Items(ref="#/components/schemas/UserResponse"),
     *                          ),
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="400",
     *         description="Failed.",
     *
     *         @OA\JsonContent(ref="#/components/schemas/PostNotExistResponse")
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
     * @param int $id
     */
    public function likedUsers($id)
    {
        $post = $this->postService->find($id);

        return $this->responseSuccess(data: [
            'users' => UserResource::collection($post->load([
                'likedUsers' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                },
            ])->likedUsers),
        ]);
    }
}
