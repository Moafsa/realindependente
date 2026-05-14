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
        \Illuminate\Support\Facades\View::composer(['layouts.site', 'site.*', 'layouts.dashboard', 'layouts.portal', 'layouts.admin'], function ($view) {
            // Não sobrescrever as configurações no editor do site, que precisa da coleção completa
            if ($view->getName() === 'site.editor') {
                return;
            }

            try {
                $settings = \App\Models\SiteSetting::getPublicSettings()->pluck('value', 'key')->toArray();
                $view->with('settings', $settings);
            } catch (\Throwable $e) {
                // Fallback for when tables don't exist yet
                $view->with('settings', []);
            }
        });

        // Share dashboard notifications
        \Illuminate\Support\Facades\View::composer(['layouts.dashboard', 'layouts.portal', 'layouts.admin'], function ($view) {
            if (auth()->check()) {
                try {
                    $user = auth()->user();
                    $isAdmin = $user->role === 'admin';
                    
                    $msgQuery = \App\Models\Message::whereNull('read_at');
                    if ($isAdmin) {
                        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                        $msgQuery->whereIn('receiver_id', $adminIds);
                    } else {
                        $msgQuery->where('receiver_id', $user->id);
                    }
                    
                    $unreadMessagesCount = $msgQuery->count();
                    $pendingCount = \App\Models\AiGeneratedContent::where('status', 'pending')->count();
                    
                    $muralCount = 0;
                    if (!$isAdmin && $user->role === 'athlete' && $user->athlete) {
                        $muralCount = \App\Models\MuralNotice::where(function($q) use ($user) {
                                $q->where('team_id', $user->athlete->team_id)->orWhereNull('team_id');
                            })
                            ->where('created_at', '>=', now()->subDay())
                            ->count();
                    }
                    
                    $view->with([
                        'unreadMessagesCount' => $unreadMessagesCount,
                        'pendingCount' => $pendingCount,
                        'muralCount' => $muralCount,
                        'totalNotifications' => $unreadMessagesCount + $pendingCount + $muralCount
                    ]);
                } catch (\Throwable $e) {
                    $view->with([
                        'unreadMessagesCount' => 0,
                        'pendingCount' => 0,
                        'muralCount' => 0,
                        'totalNotifications' => 0
                    ]);
                }
            }
        });
    }
}
