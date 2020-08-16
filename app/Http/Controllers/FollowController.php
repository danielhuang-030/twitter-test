<?php

namespace App\Http\Controllers;

use App\Http\Requests\Follow\FollowingRequest;
use App\Http\Requests\Follow\UnfollowingRequest;
use App\Services\FollowService;
use Auth;
use Symfony\Component\HttpFoundation\Response;

class FollowController extends Controller
{
    /**
     * FollowService.
     *
     * @var FollowService
     */
    protected $followService;

    /**
     * construct.
     *
     * @param FollowService $followService
     */
    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    /**
     * following.
     *
     * @OA\Patch(
     *     path="/api/following/{id}",
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
     * @param FollowingRequest $request
     */
    public function following(FollowingRequest $request, $id)
    {
        if (!$this->followService->follow($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully followed user!',
        ]);
    }

    /**
     * unfollow.
     *
     * @OA\Delete(
     *     path="/api/following/{id}",
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
     * @param UnfollowingRequest $request
     * @param int                $id
     */
    public function unfollow(UnfollowingRequest $request, $id)
    {
        if (!$this->followService->unfollow($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully unfollowed user!',
        ]);
    }
}
