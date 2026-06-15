<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site settings with all site views
        \Illuminate\Support\Facades\View::composer(['layouts.site', 'site.*', 'layouts.dashboard', 'layouts.portal', 'layouts.admin', 'marketing.*', 'auth.*'], function ($view) {
            // Não sobrescrever as configurações no editor do site, que precisa da coleção completa
            if ($view->getName() === 'site.editor') {
                return;
            }

            try {
                $settings = \App\Models\SiteSetting::getPublicSettings()->pluck('value', 'key')->toArray();
                $view->with('settings', $settings);

                // Add formatted contact data for marketing site
                $contact = [
                    'email' => $settings['superadmin_email'] ?? 'suporte@nexts.com',
                    'phone' => $settings['superadmin_phone'] ?? '(00) 0000-0000',
                    'whatsapp' => $settings['superadmin_whatsapp'] ?? '',
                    'address' => $settings['superadmin_address'] ?? 'Rua Exemplo, 123',
                    'instagram' => $settings['superadmin_instagram'] ?? '#',
                    'facebook' => $settings['superadmin_facebook'] ?? '#',
                    'linkedin' => $settings['superadmin_linkedin'] ?? '#',
                ];
                $view->with('marketing_contact', $contact);
                $view->with('contact', $contact); // For backward compatibility with existing views
            } catch (\Throwable $e) {
                // Fallback for when tables don't exist yet
                $view->with('settings', []);
                $view->with('contact', [
                    'email' => 'suporte@nexts.com',
                    'phone' => '(00) 0000-0000',
                    'whatsapp' => '',
                    'address' => 'Rua Exemplo, 123',
                    'instagram' => '#',
                    'facebook' => '#',
                    'linkedin' => '#',
                ]);
            }
        });

        // Share dashboard notifications
        \Illuminate\Support\Facades\View::composer(['layouts.dashboard', 'layouts.portal', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                try {
                    $user = auth()->user();
                    $isAdmin = $user->role === 'admin';
                    $isCoach = $user->role === 'coach';
                    
                    // Base counts
                    $unreadMessagesCount = 0;
                    $pendingCount = 0;
                    $muralCount = 0;

                    // Message counts
                    try {
                        $msgQuery = \App\Models\Message::whereNull('read_at');
                        if ($isAdmin) {
                            $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                            $msgQuery->whereIn('receiver_id', $adminIds);
                        } elseif ($isCoach) {
                            // Messages for the coach OR from their athletes to the club
                            try {
                                $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                                $msgQuery->where(function($q) use ($user, $coachTeams) {
                                    $q->where('receiver_id', $user->id)
                                      ->orWhereHas('sender.athlete', function($aq) use ($coachTeams) {
                                          $aq->whereIn('team_id', $coachTeams);
                                      });
                                });
                            } catch (\Throwable $e) {
                                $msgQuery->where('receiver_id', $user->id);
                            }
                        } else {
                            $msgQuery->where('receiver_id', $user->id);
                        }
                        $unreadMessagesCount = $msgQuery->count();
                    } catch (\Throwable $e) {}
                    
                    // Tenant-specific counts
                    if (tenancy()->initialized) {
                        $lastPendingAthleteId = null;
                        try {
                            if ($isAdmin || $isCoach) {
                                $pendingQuery = \App\Models\AiGeneratedContent::where('status', 'pending');
                                if ($isCoach) {
                                    $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                                    $pendingQuery->whereHas('athlete', function($q) use ($coachTeams) {
                                        $q->whereIn('team_id', $coachTeams);
                                    });
                                }
                                $pendingCount = $pendingQuery->count();
                                if ($pendingCount === 1) {
                                    $lastPendingAthleteId = $pendingQuery->first()?->athlete_id;
                                }
                            }
                        } catch (\Throwable $e) {}

                        try {
                            if (!$isAdmin && $user->role === 'athlete' && $user->athlete) {
                                $muralCount = \App\Models\MuralNotice::where(function($q) use ($user) {
                                        $q->where('team_id', $user->athlete->team_id)->orWhereNull('team_id');
                                    })
                                    ->where('created_at', '>=', now()->subDay())
                                    ->count();
                            }
                        } catch (\Throwable $e) {}
                    }
                    
                    // Additional info for notifications
                    $lastAthleteId = null;
                    if ($isAdmin && $unreadMessagesCount > 0) {
                        try {
                            $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                            $lastMessage = \App\Models\Message::whereIn('receiver_id', $adminIds)
                                ->whereNull('read_at')
                                ->latest()
                                ->first();
                            
                            if ($lastMessage && $lastMessage->sender && $lastMessage->sender->athlete) {
                                $lastAthleteId = $lastMessage->sender->athlete->id;
                            }
                        } catch (\Throwable $e) {}
                    }
                    
                    $view->with([
                        'unreadMessagesCount' => $unreadMessagesCount,
                        'pendingCount' => $pendingCount,
                        'muralCount' => $muralCount,
                        'totalNotifications' => $unreadMessagesCount + $pendingCount + $muralCount,
                        'isAdmin' => $isAdmin,
                        'isCoach' => $isCoach,
                        'lastAthleteId' => $lastAthleteId,
                        'lastPendingAthleteId' => $lastPendingAthleteId,
                    ]);
                } catch (\Throwable $e) {
                    $view->with([
                        'unreadMessagesCount' => 0,
                        'pendingCount' => 0,
                        'muralCount' => 0,
                        'totalNotifications' => 0,
                        'isAdmin' => false,
                        'isCoach' => false,
                        'lastAthleteId' => null,
                        'lastPendingAthleteId' => null,
                    ]);
                }
            }
        });

        // Register UploadedFile macro for optimized image storage
        \Illuminate\Http\UploadedFile::macro('storeOptimized', function ($path, $disk = null) {
            $mime = $this->getMimeType();
            
            // If it's not an image or it's an SVG/GIF, store it normally
            if (!str_starts_with($mime, 'image/') || in_array($mime, ['image/svg+xml', 'image/gif'])) {
                return $this->store($path, $disk);
            }

            try {
                // Read image using Intervention Image v3
                $imageManager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $image = $imageManager->read($this->getPathname());

                // Resize down to 1920px max width while keeping aspect ratio
                if ($image->width() > 1920) {
                    $image->scaleDown(width: 1920);
                }

                // Convert to WebP format with 80% quality
                $encoded = $image->toWebp(80);
                
                // Generate filename and store
                $filename = \Illuminate\Support\Str::random(40) . '.webp';
                $fullPath = rtrim($path, '/') . '/' . $filename;
                
                \Illuminate\Support\Facades\Storage::disk($disk)->put($fullPath, (string) $encoded);
                
                return $fullPath;
            } catch (\Exception $e) {
                // If anything fails, fallback to normal store
                \Illuminate\Support\Facades\Log::error('Image optimization failed: ' . $e->getMessage());
                return $this->store($path, $disk);
            }
        });
    }
}
