<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * PostService.
     *
     * @var PostService
     */
    protected $postService;

    /**
     * construct.
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $this->postService->add($request->all(), data_get(Auth::user(), 'id', 0));
        if (null === $post) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response()->json($this->postService->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = $this->postService->edit($request->all(), $id, data_get(Auth::user(), 'id', 0));
        if (null === $post) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->postService->del($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully deleted post!',
        ]);
    }

    /**
     * like.
     *
     * @param int $id
     */
    public function like($id)
    {
        if (!$this->postService->like($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully liked post!',
        ]);
    }

    /**
     * dislike.
     *
     * @param int $id
     */
    public function dislike($id)
    {
        if (!$this->postService->dislike($id, data_get(Auth::user(), 'id', 0))) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'message' => 'Successfully disliked post!',
        ]);
    }

    /**
     * liked users.
     *
     * @param int $id
     */
    public function likedUsers($id)
    {
        $post = $this->postService->find($id);
        if (null === $post) {
            return response()->json([
                'message' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($post->load(['likedUsers' => function ($query) {
            $query->orderBy('updated_at', 'desc');
        }])->likedUsers);
    }
}
