<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * List all posts for administration.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending_approval');
        $posts = Post::where('status', $status)->orderBy('created_at', 'desc')->paginate(10);
        
        $stats = [
            'pending' => Post::where('status', 'pending_approval')->count(),
            'scheduled' => Post::where('status', 'scheduled')->count(),
            'published' => Post::where('status', 'published')->count(),
        ];

        return view('admin.posts.index', compact('posts', 'stats', 'status'));
    }

    /**
     * Approve a post (set to scheduled).
     */
    public function approve(Post $post)
    {
        $post->update([
            'status' => 'scheduled',
            'scheduled_at' => null, // Será preenchido pelo AutomationService ou manualmente
        ]);

        return back()->with('success', 'Post aprovado! Ele será agendado automaticamente seguindo a frequência configurada.');
    }

    /**
     * Reject/Delete a post.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post removido com sucesso.');
    }

    /**
     * Update automation settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'blog_post_frequency' => 'required|integer|min:0|max:7',
            'blog_post_context' => 'required|string|max:1000',
            'blog_auto_generate' => 'sometimes|boolean',
        ]);

        SiteSetting::set('blog_post_frequency', $request->blog_post_frequency, 'number', 'Frequência de posts por semana');
        SiteSetting::set('blog_post_context', $request->blog_post_context, 'textarea', 'Contexto para IA gerar posts');
        SiteSetting::set('blog_auto_generate', $request->has('blog_auto_generate'), 'boolean', 'Habilitar geração automática por IA');

        return back()->with('success', 'Configurações de automação atualizadas!');
    }

    /**
     * Edit post content.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update post content.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'scheduled_at' => 'nullable|date',
        ]);

        $post->update($data);

        return redirect()->route('admin.posts.index', ['status' => $post->status])
                         ->with('success', 'Post atualizado com sucesso.');
    }
}
