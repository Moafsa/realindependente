<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ChatSentinel;

class CommunicationController extends Controller
{
    /**
     * Display communication page (messages, chat).
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        $isCoach = $user->role === 'coach';
        
        if (!$isAdmin && !$isCoach && !$user->athlete) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $targetAthlete = null;
        $chatTarget = null;

        if ($isAdmin || $isCoach) {
            $athleteId = $request->query('athlete_id');
            if ($athleteId) {
                $targetAthlete = \App\Models\Athlete::find($athleteId);
                if ($isCoach && $targetAthlete && $targetAthlete->team->coach_id !== $user->id) {
                    $targetAthlete = null;
                }
                $chatTarget = $targetAthlete->user ?? null;
            }
            
            $staffIds = \App\Models\User::whereIn('role', ['admin', 'coach'])->pluck('id')->toArray();
            
            $athleteQuery = \App\Models\Athlete::where('is_active', true);
            if ($isCoach) {
                $athleteQuery->whereIn('team_id', \App\Models\Team::where('coach_id', $user->id)->pluck('id'));
            }

            $athletes = $athleteQuery->with('user')
                ->get()
                ->map(function($a) use ($user, $isAdmin) {
                    // Count unread messages from this athlete to current user (or any admin pool)
                    $msgQuery = \App\Models\Message::where('sender_id', $a->user_id)
                        ->whereNull('read_at');
                    
                    if ($isAdmin) {
                        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                        $msgQuery->whereIn('receiver_id', $adminIds);
                    } elseif ($isCoach) {
                        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                        $staffIds = array_merge($adminIds, [$user->id]);
                        $msgQuery->whereIn('receiver_id', $staffIds);
                    } else {
                        $msgQuery->where('receiver_id', $user->id);
                    }
                    
                    $a->unread_count = $msgQuery->count();
                    
                    // Get last activity date
                    $lastMsg = \App\Models\Message::where(function($q) use ($a, $user) {
                        $q->where('sender_id', $a->user_id)->where('receiver_id', $user->id);
                    })->orWhere(function($q) use ($a, $user) {
                        $q->where('sender_id', $user->id)->where('receiver_id', $a->user_id);
                    })->latest()->first();
                    
                    $a->last_activity = $lastMsg ? $lastMsg->created_at : null;
                    return $a;
                })
                ->sortByDesc('last_activity');
        } else {
            $targetAthlete = $user->athlete;
            $chatTarget = $targetAthlete->team->coach ?? \App\Models\User::where('role', 'admin')->first();
            $athletes = collect([]);
        }

        // Get messages between current user and chat target
        $messages = [];
        if ($chatTarget) {
            // MARK AS READ (Admin Pool Logic)
            if ($isAdmin) {
                // Any Admin viewing the athlete marks all messages from athlete to ANY admin as read
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                \App\Models\Message::where('sender_id', $chatTarget->id)
                    ->whereIn('receiver_id', $adminIds)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } elseif ($isCoach) {
                // Coach viewing marks messages from athlete to them OR to ANY admin as read
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $receiverIds = array_merge($adminIds, [$user->id]);
                \App\Models\Message::where('sender_id', $chatTarget->id)
                    ->whereIn('receiver_id', $receiverIds)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } else {
                // Athlete viewing marks all messages from ANY admin OR coach as read
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $coachId = $targetAthlete->team->coach_id ?? null;
                $senderIds = array_filter(array_merge($adminIds, [$coachId]));
                
                \App\Models\Message::whereIn('sender_id', $senderIds)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
            if ($isAdmin) {
                // Admin viewing athlete: show messages between this athlete and ANY admin
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $messages = \App\Models\Message::where(function($query) use ($chatTarget, $adminIds) {
                        $query->where('sender_id', $chatTarget->id)
                              ->whereIn('receiver_id', $adminIds);
                    })->orWhere(function($query) use ($chatTarget, $adminIds) {
                        $query->whereIn('sender_id', $adminIds)
                              ->where('receiver_id', $chatTarget->id);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            } elseif ($isCoach) {
                // Coach viewing athlete: show messages between athlete and coach OR athlete and ANY admin
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $staffIds = array_merge($adminIds, [$user->id]);
                $messages = \App\Models\Message::where(function($query) use ($chatTarget, $staffIds) {
                        $query->where('sender_id', $chatTarget->id)
                              ->whereIn('receiver_id', $staffIds);
                    })->orWhere(function($query) use ($chatTarget, $staffIds) {
                        $query->whereIn('sender_id', $staffIds)
                              ->where('receiver_id', $chatTarget->id);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                // Athlete viewing: show messages between them and ANY admin OR their coach
                $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                $messages = \App\Models\Message::where(function($query) use ($user, $adminIds) {
                        $query->where('sender_id', $user->id)
                              ->whereIn('receiver_id', $adminIds);
                    })->orWhere(function($query) use ($user, $adminIds) {
                        $query->whereIn('sender_id', $adminIds)
                              ->where('receiver_id', $user->id);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        $view = $isAdmin || $user->role === 'coach' ? 'admin.communication' : 'portal.communication';
        
        // Fetch Mural Notices
        if ($isAdmin) {
            $notices = \App\Models\MuralNotice::with(['sender', 'team'])->orderBy('created_at', 'desc')->get();
            $teams = \App\Models\Team::all();
        } elseif ($user->role === 'coach') {
            $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id');
            $notices = \App\Models\MuralNotice::whereIn('team_id', $coachTeams)->orWhereNull('team_id')
                ->with(['sender', 'team'])
                ->orderBy('created_at', 'desc')
                ->get();
            $teams = \App\Models\Team::where('coach_id', $user->id)->get();
        } else {
            $athleteTeamId = $user->athlete->team_id ?? null;
            $notices = \App\Models\MuralNotice::where(function($query) use ($athleteTeamId) {
                $query->whereNull('team_id');
                if ($athleteTeamId) {
                    $query->orWhere('team_id', $athleteTeamId);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();
            $teams = [];
        }

        return view($view, [
            'messages' => $messages,
            'coach' => !$isAdmin && $user->role !== 'coach' ? $chatTarget : null,
            'athlete' => !$isAdmin && $user->role !== 'coach' ? $targetAthlete : null,
            'chatTarget' => $chatTarget,
            'athletes' => $athletes,
            'notices' => $notices,
            'teams' => $teams,
            'isAdmin' => $isAdmin || $user->role === 'coach'
        ]);
    }

    public function muralStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'team_id' => 'nullable|exists:teams,id',
            'priority' => 'required|in:low,medium,high,important',
        ]);

        \App\Models\MuralNotice::create([
            'sender_id' => Auth::id(),
            'team_id' => $request->team_id ?: null,
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
        ]);

        return redirect()->back()->with('success', 'Aviso publicado no mural com sucesso!');
    }

    public function getMessages(Request $request)
    {
        $user = Auth::user();
        $targetId = $request->get('target_id');
        $isAdmin = $user->role === 'admin';

        if (!$targetId) return response()->json(['success' => false]);

        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();

        // MARK AS READ BEFORE FETCHING (Admin Pool Logic)
        if ($isAdmin) {
            \App\Models\Message::where('sender_id', $targetId)
                ->whereIn('receiver_id', $adminIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } else {
            $athlete = $user->athlete;
            $coachId = $athlete->team->coach_id ?? null;
            $senderIds = array_filter(array_merge($adminIds, [$coachId]));
            
            \App\Models\Message::whereIn('sender_id', $senderIds)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        if ($isAdmin) {
            // Admin viewing athlete: show messages between this athlete and ANY admin
            $messages = \App\Models\Message::where(function($query) use ($targetId, $adminIds) {
                    $query->where('sender_id', $targetId)->whereIn('receiver_id', $adminIds);
                })->orWhere(function($query) use ($targetId, $adminIds) {
                    $query->whereIn('sender_id', $adminIds)->where('receiver_id', $targetId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $athlete = $user->athlete;
            $coachId = $athlete->team->coach_id ?? null;
            $senderIds = array_filter(array_unique(array_merge($adminIds, [$coachId])));

            // Athlete viewing: show messages between them and ANY admin OR their coach
            $messages = \App\Models\Message::where(function($query) use ($user, $senderIds) {
                    $query->where('sender_id', $user->id)->whereIn('receiver_id', $senderIds);
                })->orWhere(function($query) use ($user, $senderIds) {
                    $query->whereIn('sender_id', $senderIds)->where('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function($m) use ($user) {
                return [
                    'id' => $m->id,
                    'content' => $m->content,
                    'sender_id' => $m->sender_id,
                    'is_own' => $m->sender_id === $user->id,
                    'time' => $m->created_at->format('H:i'),
                    'full_date' => $m->created_at->format('Y-m-d'),
                    'attachment_url' => $m->attachment_path ? \Storage::url($m->attachment_path) : null,
                    'attachment_type' => $m->attachment_type,
                    'read_at' => $m->read_at
                ];
            })
        ]);
    }

    protected ChatSentinel $sentinel;

    public function __construct(ChatSentinel $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Store a new message.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:20480', // 20MB max
        ]);

        $user = Auth::user();

        // AI Moderation Check
        if ($request->content) {
            $analysis = $this->sentinel->analyze($request->content, $user);
            if (!$analysis['isSafe']) {
                if ($analysis['blockAccount']) {
                    $this->sentinel->notifyAdmin($user, "Bloqueio automático por abuso repetido.");
                }

                return response()->json([
                    'success' => false,
                    'message' => $analysis['warning'],
                    'blocked' => $analysis['blockAccount']
                ], 403);
            }
        }

        try {
            $attachmentPath = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $mime = $file->getMimeType();
                
                if (str_starts_with($mime, 'image/')) {
                    $attachmentType = 'image';
                } elseif (str_starts_with($mime, 'video/')) {
                    $attachmentType = 'video';
                } elseif (str_starts_with($mime, 'audio/')) {
                    $attachmentType = 'audio';
                } else {
                    $attachmentType = 'document';
                }

                $attachmentPath = $file->store('chat/attachments');
            }

            if (!$request->content && !$attachmentPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'A mensagem não pode estar vazia.'
                ], 400);
            }

            $message = \App\Models\Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content ?? '',
                'attachment_path' => $attachmentPath,
                'attachment_type' => $attachmentType,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mensagem enviada com sucesso',
                'data' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'attachment_url' => $attachmentPath ? \Storage::url($attachmentPath) : null,
                    'attachment_type' => $attachmentType,
                    'created_at' => $message->created_at->format('H:i')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending message', [
                'error' => $e->getMessage(),
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
    public function markAsRead(Request $request, $messageId)
    {
        try {
            if (str_starts_with($messageId, 'all_from_')) {
                $senderId = str_replace('all_from_', '', $messageId);
                \App\Models\Message::where('sender_id', $senderId)
                    ->where('receiver_id', Auth::id())
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);

                return response()->json([
                    'success' => true,
                    'message' => 'Todas as mensagens marcadas como lidas'
                ]);
            }

            $message = \App\Models\Message::where('id', $messageId)
                ->where('receiver_id', Auth::id())
                ->firstOrFail();

            $message->update(['read_at' => now()]);

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

    public function getNotificationCounts()
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';
        $isCoach = $user->role === 'coach';
        
        $query = \App\Models\Message::whereNull('read_at');

        if ($isAdmin) {
            $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
            $query->whereIn('receiver_id', $adminIds);
        } elseif ($isCoach) {
            $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
            $query->where(function($q) use ($user, $coachTeams) {
                $q->where('receiver_id', $user->id)
                  ->orWhereHas('sender.athlete', function($aq) use ($coachTeams) {
                      $aq->whereIn('team_id', $coachTeams);
                  });
            });
        } else {
            $query->where('receiver_id', $user->id);
        }

        $unreadMessagesCount = $query->count();
        
        // Count pending AI plans
        $pendingCount = 0;
        if ($isAdmin || $isCoach) {
            $pendingQuery = \App\Models\AiGeneratedContent::where('status', 'pending');
            if ($isCoach) {
                $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                $pendingQuery->whereHas('athlete', function($q) use ($coachTeams) {
                    $q->whereIn('team_id', $coachTeams);
                });
            }
            $pendingCount = $pendingQuery->count();
        }

        // Add recent Mural Notices to athlete count
        $muralCount = 0;
        if (!$isAdmin && $user->role === 'athlete' && $user->athlete) {
            $lastMuralView = \Illuminate\Support\Facades\Cache::get('user_mural_view_' . $user->id, now()->subMonths(3));
            $muralCount = \App\Models\MuralNotice::where(function($q) use ($user) {
                    $q->where('team_id', $user->athlete->team_id)->orWhereNull('team_id');
                })
                ->where('created_at', '>', $lastMuralView)
                ->count();
        }

        $lastAthleteId = null;
        if ($isAdmin && $unreadMessagesCount > 0) {
            $lastMessage = \App\Models\Message::whereIn('receiver_id', $adminIds)
                ->whereNull('read_at')
                ->latest()
                ->first();
            
            if ($lastMessage) {
                $sender = $lastMessage->sender;
                if ($sender && $sender->athlete) {
                    $lastAthleteId = $sender->athlete->id;
                }
                
                // Add a snippet of the last message for the notification
                $lastSnippet = $lastMessage->content;
                if (!$lastSnippet && $lastMessage->attachment_path) {
                    $lastSnippet = $lastMessage->attachment_type === 'image' ? '📸 Enviou uma imagem' : '📄 Enviou um documento';
                }
                $lastSnippet = \Illuminate\Support\Str::limit($lastSnippet, 30);
            }
        }

        return response()->json([
            'success' => true,
            'unreadMessagesCount' => (int) $unreadMessagesCount,
            'pendingCount' => (int) $pendingCount,
            'muralCount' => (int) $muralCount,
            'totalNotifications' => (int) ($unreadMessagesCount + $pendingCount + $muralCount),
            'lastAthleteId' => $lastAthleteId,
            'lastSnippet' => $lastSnippet ?? null
        ]);
    }

    public function muralRead(Request $request)
    {
        $user = auth()->user();
        \Illuminate\Support\Facades\Cache::put('user_mural_view_' . $user->id, now(), now()->addMonths(1));
        
        return response()->json(['success' => true]);
    }
    public function storeTeamPlan(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'type' => 'required|in:workout_plan,meal_plan',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $athletes = \App\Models\Athlete::where('team_id', $request->team_id)->get();
        
        if ($athletes->isEmpty()) {
            return redirect()->back()->with('error', 'Esta equipe não possui atletas.');
        }

        foreach ($athletes as $athlete) {
            \App\Models\AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => $request->type,
                'status' => 'active',
                'content' => [
                    'title' => $request->title,
                    'description' => $request->content,
                    'duration' => $request->type === 'workout_plan' ? 'Definido pelo Coach' : null,
                    'difficulty' => 'Personalizado',
                    'calories' => $request->type === 'meal_plan' ? 'Definido pelo Nutricionista' : null,
                ],
                'generated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Plano coletivo enviado para ' . $athletes->count() . ' atletas!');
    }
}

