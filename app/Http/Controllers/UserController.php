<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * info
     */
    public function info(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * follow
     */
    public function follow(Request $request)
    {
        return response()->json(auth()->user()->follows->all());
    }

    /**
     * follow
     */
    public function followMe(Request $request)
    {
        return response()->json(auth()->user()->followMes->all());
    }

    /**
     * liked posts
     */
    public function likedPosts(Request $request)
    {
        $posts = auth()->user()->load(['likePosts' => function ($query) {
            $query->where('is_liked', \App\Models\PostLike::IS_LIKED_LIKE)
                ->orderBy('updated_at', 'desc');
        }])->likePosts->all();
        return response()->json($posts);
    }
}
