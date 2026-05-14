<?php

namespace App\Notifications;

use App\Services\WuzapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppResetPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['whatsapp'];
    }

    /**
     * Send the notification via WhatsApp.
     */
    public function toWhatsApp($notifiable)
    {
        $phone = $notifiable->phone;
        if (!$phone) {
            Log::warning("WhatsAppResetPassword: User {$notifiable->id} has no phone number.");
            return;
        }

        // Gera a URL de reset
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Se estiver em tenant, precisamos garantir que a URL use o domínio correto
        if (function_exists('tenancy') && tenancy()->initialized) {
            $url = tenant_route(tenant('domains')->first()->domain, 'password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        }

        $message = "🔐 *Recuperação de Senha*\n\n";
        $message .= "Olá *{$notifiable->name}*,\n\n";
        $message .= "Você solicitou a recuperação de senha da sua conta.\n\n";
        $message .= "Clique no link abaixo para criar uma nova senha:\n";
        $message .= "{$url}\n\n";
        $message .= "Este link expirará em breve.\n";
        $message .= "Se você não solicitou isso, ignore esta mensagem.\n\n";
        $message .= "Atenciosamente,\n";
        $message .= "Equipe " . config('app.name');

        $wuzapi = new WuzapiService();
        return $wuzapi->sendMessage($phone, $message);
    }
}
