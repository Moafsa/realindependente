<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Support\Str;

class TournamentTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar um Torneio de Teste
        $tournament = Tournament::updateOrCreate(
            ['slug' => 'copa-real-2026'],
            [
                'name' => 'Copa Real 2026',
                'description' => 'Campeonato de teste para validar a geração de rodadas.',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addMonths(2),
                'format' => 'league',
                'status' => 'draft',
            ]
        );

        // 2. Criar 6 Times de Teste (para o algoritmo Round-Robin)
        $teamNames = [
            'Real Independente FC',
            'Academia de Craques',
            'Estrela do Norte',
            'Titãs do Futebol',
            'Leões da Vila',
            'Dragões Vermelhos'
        ];

        foreach ($teamNames as $name) {
            Team::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => 'sub-17',
                    'level' => 'advanced',
                    'is_active' => true,
                    'is_public' => true,
                ]
            );
        }
    }
}
