<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\AIService;
use Illuminate\Support\Facades\Log;

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

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|string|in:draft,pending_approval,published',
            'image_url' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->storeOptimized('blog');
            $data['image_url'] = $path;
        }

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post criado com sucesso.');
    }

    /**
     * Generate a PRO blog post using AI.
     */
    public function aiGenerate(Request $request, AIService $aiService)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'word_count' => 'required|string',
            'keywords' => 'nullable|string|max:255',
        ]);

        try {
            $result = $aiService->generateProBlogPost(
                $request->topic,
                $request->description,
                $request->word_count,
                $request->keywords ?? ''
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar post PRO', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar post com a IA. Tente novamente mais tarde.'
            ], 500);
        }
    }
}
