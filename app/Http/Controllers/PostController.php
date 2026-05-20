<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function store(StorePostRequest $request)
    {
        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'published_at' => $request->published_at,
        ]);

        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }

        return redirect()->route('posts.show', $post)
                         ->with('success', 'Post creado exitosamente');
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        // 2. Actualizar datos base
        $post->update($request->validated());

        // 3. rsync de las etiquetas (borra las viejas, guarda las nuevas)
        $post->tags()->sync($request->tags);

        return redirect()->route('posts.show', $post)
                         ->with('success', 'Post actualizado');
    }
}
