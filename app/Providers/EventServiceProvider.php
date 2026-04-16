<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Eventos de cobrança
        \App\Events\ChargeGenerated::class => [
            \App\Listeners\SendChargeNotificationListener::class,
        ],
        
        \App\Events\ChargeOverdue::class => [
            \App\Listeners\SendChargeReminderListener::class,
        ],
        
        // Eventos de treino
        \App\Events\TrainingScheduled::class => [
            \App\Listeners\SendTrainingReminderListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
