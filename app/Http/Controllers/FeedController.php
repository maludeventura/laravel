<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $posts = Post::with(['user', 'likes'])->orderBy('created_at', 'desc')->get();

        $posts->transform(function ($post) use ($userId) {
            $post->likes_count = $post->likes->count();
            $post->liked = $post->likes->contains('user_id', $userId);
            unset($post->likes);
            return $post;
        });

        return $posts;
    }
}