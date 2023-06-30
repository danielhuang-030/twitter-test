<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\Follow\FollowingRequest;
use App\Http\Requests\Api\v1\Follow\UnfollowRequest;
use App\Services\FollowService;

class FollowController extends BaseController
{
    public function __construct(protected FollowService $followService)
    {
        parent::__construct();
    }

    /**
     * following.
     *
     * @OA\Patch(
     *     path="/api/v1/following/{id}",
     *     summary="User Following",
     *     description="User following",
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
     *                          example="Successfully followed user!",
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
     *                          example="502002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Following failed.",
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
     * @param FollowingRequest $request
     */
    public function following(FollowingRequest $request, $id)
    {
        $this->followService->follow($id, (int) auth()->user()?->id);

        return $this->responseSuccess(message: 'Successfully followed user!');
    }

    /**
     * unfollow.
     *
     * @OA\Delete(
     *     path="/api/v1/following/{id}",
     *     summary="User Unfollow",
     *     description="User unfollow",
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
     *                          example="Successfully unfollowed user!",
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
     *                          example="502002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Unfollow failed.",
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
     * @param UnfollowRequest $request
     * @param int             $id
     */
    public function unfollow(UnfollowRequest $request, $id)
    {
        $this->followService->unfollow($id, (int) auth()->user()?->id);

        return $this->responseSuccess(message: 'Successfully unfollowed user!');
    }
}
