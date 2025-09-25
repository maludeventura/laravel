<?php
namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, $postId)
    {
        $user = $request->user();
        $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'post_id' => $postId]);
            $liked = true;
        }

        $count = Like::where('post_id', $postId)->count();

        return response()->json(['liked' => $liked, 'likes' => $count]);
    }

    public function count($postId, Request $request)
    {
        $user = $request->user();
        $likes = Like::where('post_id', $postId)->count();
        $liked = Like::where('post_id', $postId)->where('user_id', $user->id)->exists();

        return response()->json(['likes' => $likes, 'liked' => $liked]);
    }
}