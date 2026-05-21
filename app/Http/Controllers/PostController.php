<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Attachment;
use App\Services\FileService;
use App\Http\Requests\StorePostWithAttachmentsRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('posts.create', compact('categories'));
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function store(StorePostWithAttachmentsRequest $request)
    {
        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'published_at' => $request->published_at,
        ]);

        if ($request->hasFile('attachments')) {
            $fileService = new FileService();

            // Iteramos sobre cada archivo subido
            foreach ($request->file('attachments') as $file) {
                $fileService->storeAttachment($file, $post->id);
            }
        }

        return redirect()->route('posts.show', $post)
                         ->with('success', 'Post y archivos guardados exitosamente');
    }

    public function destroyAttachment(Attachment $attachment)
    {
        // 1. Validamos que el usuario sea el dueño del post
        $this->authorize('delete', $attachment->post);

        // 2. Llamamos al Daemon para destruir el archivo físico
        $fileService = new FileService();
        $fileService->deleteAttachment($attachment);

        return redirect()->back()->with('success', 'Archivo eliminado');
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
