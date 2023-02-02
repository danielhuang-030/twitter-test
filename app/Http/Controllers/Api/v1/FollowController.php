<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ApiResponseCode;
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
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully followed user!",
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
     *                         example="The selected id is invalid.",
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
     *                          property="code",
     *                          type="string",
     *                          example="999002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Unauthorized",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          example="{}",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
     *
     * @param FollowingRequest $request
     */
    public function following(FollowingRequest $request, $id)
    {
        if (!$this->followService->follow($id, (int) data_get(\Auth::user(), 'id'))) {
            return $this->responseFail(code: ApiResponseCode::ERROR_FOLLOW_FOLLOWING->value);
        }

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
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         format="string",
     *                         description="message",
     *                         example="Successfully unfollowed user!",
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
     *                         example="The selected id is invalid.",
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
     *                          property="code",
     *                          type="string",
     *                          example="999002",
     *                     ),
     *                     @OA\Property(
     *                          property="message",
     *                          type="string",
     *                          example="Unauthorized",
     *                     ),
     *                     @OA\Property(
     *                          property="data",
     *                          type="object",
     *                          example="{}",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
     *
     * @param UnfollowRequest $request
     * @param int             $id
     */
    public function unfollow(UnfollowRequest $request, $id)
    {
        if (!$this->followService->unfollow($id, (int) data_get(\Auth::user(), 'id'))) {
            return $this->responseFail(code: ApiResponseCode::ERROR_FOLLOW_UNFOLLOW->value);
        }

        return $this->responseSuccess(message: 'Successfully unfollowed user!');
    }
}
