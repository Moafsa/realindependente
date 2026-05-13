<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tenant $tenant;
    public string $subdomain;
    public string $adminEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(Tenant $tenant, string $subdomain, string $adminEmail)
    {
        $this->tenant = $tenant;
        $this->subdomain = $subdomain;
        $this->adminEmail = $adminEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Nexts!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-welcome',
            with: [
                'tenant' => $this->tenant,
                'subdomain' => $this->subdomain,
                'adminEmail' => $this->adminEmail,
                'loginUrl' => 'https://' . $this->subdomain . '.' . config('tenancy.central_domains')[0] . '/login',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

