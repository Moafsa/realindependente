<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateAthleteSubcategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'athletes:update-subcategories';

    protected $description = 'Atualiza as subcategorias de todos os atletas em todos os tenants baseado no ano atual';

    public function handle()
    {
        $this->info('Iniciando atualização de subcategorias...');

        tenancy()->runForMultiple(null, function ($tenant) {
            $this->info("Processando tenant: {$tenant->id}");
            \App\Models\Athlete::updateAllSubcategories();
        });

        $this->info('Atualização concluída com sucesso!');
    }
}
