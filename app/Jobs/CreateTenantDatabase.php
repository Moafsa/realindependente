<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class CreateTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Tenant $tenant;

    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('CreateTenantDatabase: Iniciando criação do banco de dados do tenant', [
                'tenant_id' => $this->tenant->id,
                'tenant_name' => $this->tenant->name,
            ]);

            // Inicializa o contexto do tenant
            tenancy()->initialize($this->tenant);

            // Executa as migrations do tenant
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            Log::info('CreateTenantDatabase: Migrations executadas com sucesso', [
                'tenant_id' => $this->tenant->id,
            ]);

            // Executa os seeders do tenant
            Artisan::call('db:seed', [
                '--class' => 'TenantSeeder',
                '--force' => true,
            ]);

            Log::info('CreateTenantDatabase: Seeders executados com sucesso', [
                'tenant_id' => $this->tenant->id,
            ]);

            Log::info('CreateTenantDatabase: Banco de dados do tenant criado com sucesso', [
                'tenant_id' => $this->tenant->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('CreateTenantDatabase: Erro ao criar banco de dados do tenant', [
                'tenant_id' => $this->tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }
    }
}

