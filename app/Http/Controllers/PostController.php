<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * PostService
     *
     * @var PostService
     */
    protected $postService;

    /**
     * construct
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->postService->list();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $this->postService->add($request->all(), auth()->user()->id);
        if (null === $post) {
            return response()->json([
                'message' => 'error'
            ], 403);
        }

        return response()->json([
            'message' => 'Successfully add post!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return response()->json($this->postService->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = $this->postService->edit($request->all(), $id, auth()->user()->id);
        if (null === $post) {
            return response()->json([
                'message' => 'error'
            ], 403);
        }

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!$this->postService->del($id, auth()->user()->id)) {
            return response()->json([
                'message' => 'error'
            ], 403);
        }

        return response()->json([
            'message' => 'Successfully deleted post!'
        ]);
    }
}
