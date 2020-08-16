<?php

namespace App\Http\Controllers;

use App\Http\Requests\Follow\StoreRequest;
use App\Services\FollowService;
use Auth;
use Illuminate\Http\Request;
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
     * store.
     *
     * @OA\Post(
     *     path="/api/following",
     *     summary="User Following",
     *     description="User following",
     *     tags={"User"},
     *     security={
     *         {
     *             "passport": {},
     *         },
     *     },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"user_id"},
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     format="int64",
     *                     description="user id",
     *                     example="2",
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
     *                         example="The user id field is required.",
     *                     ),
     *                 ),
     *             ),
     *         },
     *     ),
     * )
     *
     * @param Request $request
     */
    public function store(StoreRequest $request)
    {
        if (!$this->followService->follow(
            $request->input('user_id', 0),
            data_get(Auth::user(), 'id', 0)
        )) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully followed user!',
        ]);
    }

    /**
     * destroy.
     *
     * @OA\Delete(
     *     path="/api/following/{id}",
     *     summary="User Unfollowing",
     *     description="User unfollowing",
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
     * )
     *
     * @param int $id
     */
    public function destroy($id)
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
