<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('user')->orderBy('created_at', 'desc')->get();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $dados = $request->validate([
            'description' => 'required|string|max:255',
            'picture' => 'nullable|string|max:255'
        ]);

        $post = Post::create([
            'description' => $dados['description'],
            'picture' => $dados['picture'] ?? '',
            'data' => now()->format('Y-m-d H:i:s'),
            'user_id' => $request->user()->id
        ]); 
        return response()->json(Post::with('user')->find($post->id), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId)
    {
        // Obtém o post
        $post = Post::find($postId);
    
        // Verifica se o post existe e se pertence ao usuário logado
        if (!$post || $post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Ação não permitida'], 403);
        }
    
        // Deleta o post
        $post->delete();
    
        return response()->json(['message' => 'Post deletado com sucesso.']);
    }
    
}
