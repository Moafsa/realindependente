<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommunicationController extends Controller
{
    /**
     * Display communication page (messages, chat).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        // TODO: Load messages when Message model is created
        $messages = [];
        $coach = $athlete->team->coach ?? null;

        return view('portal.communication', compact('messages', 'coach', 'athlete'));
    }

    /**
     * Store a new message.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'recipient_id' => 'nullable|exists:users,id',
        ]);

        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não possui atleta associado.'
                ], 400);
            }
            
            return redirect()->back()
                ->with('error', 'Usuário não possui atleta associado.');
        }

        try {
            // TODO: Implement when Message model is created
            // For now, just log the message
            Log::info('Message sent', [
                'athlete_id' => $athlete->id,
                'message' => $request->message,
                'recipient_id' => $request->recipient_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mensagem enviada com sucesso!'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Mensagem enviada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'error' => $e->getMessage(),
                'athlete_id' => $athlete->id,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao enviar mensagem: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Erro ao enviar mensagem: ' . $e->getMessage());
        }
    }

    /**
     * Mark message as read.
     *
     * @param Request $request
     * @param int $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, int $messageId)
    {
        try {
            // TODO: Implement when Message model is created
            Log::info('Message marked as read', [
                'message_id' => $messageId,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensagem marcada como lida'
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking message as read', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar mensagem: ' . $e->getMessage()
            ], 500);
        }
    }
}

