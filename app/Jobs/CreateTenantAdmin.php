<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class CreateTenantAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Tenant $tenant;
    public array $adminData;

    /**
     * Create a new job instance.
     * 
     * @param Tenant $tenant
     * @param array $adminData ['name', 'email', 'password']
     */
    public function __construct(Tenant $tenant, array $adminData)
    {
        $this->tenant = $tenant;
        $this->adminData = $adminData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('CreateTenantAdmin: Iniciando criação do usuário admin do tenant', [
                'tenant_id' => $this->tenant->id,
                'admin_email' => $this->adminData['email'] ?? null,
            ]);

            // Inicializa o contexto do tenant
            tenancy()->initialize($this->tenant);

            // Cria ou atualiza o usuário admin
            $admin = User::updateOrCreate(
                ['email' => $this->adminData['email']],
                [
                    'name' => $this->adminData['name'],
                    'password' => Hash::make($this->adminData['password']),
                    'role' => 'admin',
                    'is_active' => true,
                    'is_super_admin' => false,
                ]
            );

            Log::info('CreateTenantAdmin: Usuário admin criado com sucesso', [
                'tenant_id' => $this->tenant->id,
                'user_id' => $admin->id,
                'user_email' => $admin->email,
            ]);

            // Finaliza o contexto do tenant
            tenancy()->end();

        } catch (\Exception $e) {
            Log::error('CreateTenantAdmin: Erro ao criar usuário admin do tenant', [
                'tenant_id' => $this->tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Finaliza o contexto do tenant em caso de erro
            tenancy()->end();

            throw $e;
        }
    }
}

