<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * info.
     */
    public function info(Request $request)
    {
        return response()->json(Auth::user());
    }

    /**
     * following.
     */
    public function follow(Request $request, int $id = 0)
    {
        return response()->json(Auth::user()->following->all());
    }

    /**
     * followers.
     */
    public function followMe(Request $request)
    {
        return response()->json(Auth::user()->followers->all());
    }

    /**
     * liked posts.
     */
    public function likedPosts(Request $request)
    {
        $posts = Auth::user()->load(['likePosts' => function ($query) {
            $query->where('is_liked', \App\Models\PostLike::IS_LIKED_LIKE)
                ->orderBy('updated_at', 'desc');
        }])->likePosts->all();

        return response()->json($posts);
    }
}
