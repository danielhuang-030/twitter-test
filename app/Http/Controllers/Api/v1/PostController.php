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
use Symfony\Component\HttpFoundation\Response;

class PostController extends BaseController
{
    /**
     * @OA\Schema(
     *     schema="PostResponse",
     *     type="object",
     *     title="Post Response",
     *
     *     @OA\Property(property="id", type="integer" ,format="int64", example=1),
     *     @OA\Property(property="user_id", type="integer", format="int64", example="1"),
     *     @OA\Property(property="content", type="string", format="string", example="test"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2020-07-31 23:54:28"),
     * )
     */
    public function __construct(protected PostService $postService)
    {
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/posts",
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
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The content field is required.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $post = $this->postService->add($request->validated(), (int) data_get(\Auth::user(), 'id'));
        if (empty($post)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_ADD->value);
        }

        return $this->responseSuccess([
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/posts/{id}",
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
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="Failed.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The selected id is invalid.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param int         $id
     * @param ShowRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowRequest $request, int $id)
    {
        $post = $this->postService->find($id);
        if (empty($post)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_NOT_EXIST->value);
        }

        return $this->responseSuccess(data: [
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/posts/{id}",
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
     *         @OA\JsonContent(ref="#/components/schemas/PostResponse")
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="Failed.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The selected id is invalid.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        $post = $this->postService->edit($request->validated(), $id, (int) data_get(\Auth::user(), 'id'));
        if (empty($post)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_EDIT->value);
        }

        return $this->responseSuccess(data: [
            'post' => PostResource::make($post),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/posts/{id}",
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
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully deleted post!",
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
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->postService->del($id, (int) data_get(\Auth::user(), 'id'))) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_DEL->value);
        }

        return $this->responseSuccess(message: 'Successfully deleted post!');
    }

    /**
     * like.
     *
     * @OA\Patch(
     *     path="/api/posts/{id}/like",
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
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully liked post!",
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The selected id is invalid.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param LikeRequest $request
     * @param int         $id
     */
    public function like(LikeRequest $request, $id)
    {
        if (!$this->postService->like($id, data_get(\Auth::user(), 'id', 0))) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_LIKE->value);
        }

        return $this->responseSuccess(message: 'Successfully liked post!');
    }

    /**
     * dislike.
     *
     * @OA\Delete(
     *     path="/api/posts/{id}/like",
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
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully disliked post!",
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
     *
     *     @OA\Response(
     *         response="422",
     *         description="Validation error.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="The selected id is invalid.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param DislikeRequest $request
     * @param int            $id
     */
    public function dislike(DislikeRequest $request, $id)
    {
        if (!$this->postService->dislike($id, data_get(\Auth::user(), 'id', 0))) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_DISLIKE->value);
        }

        return $this->responseSuccess(message: 'Successfully disliked post!');
    }

    /**
     * liked users.
     *
     * @OA\Get(
     *     path="/api/posts/{id}/liked_users",
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
     *                     type="array",
     *
     *                     @OA\Items(ref="#/components/schemas/UserResponse"),
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
     *
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized.",
     *         content={
     *
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *
     *                 @OA\Schema(
     *
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
     * @param int $id
     */
    public function likedUsers($id)
    {
        $post = $this->postService->find($id);
        if (empty($post)) {
            return $this->responseFail(code: ApiResponseCode::ERROR_POST_NOT_EXIST->value);
        }

        return $this->responseSuccess(data: [
            'users' => UserResource::collection($post->load([
                'likedUsers' => function ($query) {
                    $query->orderBy('updated_at', 'desc');
                },
            ])->likedUsers),
        ]);
    }
}