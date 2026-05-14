<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Team;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Default Admin/Coach for the club
        $adminEmail = 'admin@' . tenant('id') . '.com';
        $coachEmail = 'coach@' . tenant('id') . '.com';
        $athleteEmail = 'athlete@' . tenant('id') . '.com';

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Administrador ' . tenant('id'),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $coach = User::updateOrCreate(
            ['email' => $coachEmail],
            [
                'name' => 'Treinador ' . tenant('id'),
                'password' => Hash::make('password'),
                'role' => 'coach',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => $athleteEmail],
            [
                'name' => 'Atleta Exemplo',
                'password' => Hash::make('password'),
                'role' => 'athlete',
                'is_active' => true,
            ]
        );

        // 2. Create a default Branch
        $branch = Branch::updateOrCreate(
            ['slug' => 'sede-principal'],
            [
                'name' => 'Sede Principal',
                'address' => 'Rua do Esporte, 100',
                'city' => 'Sede',
                'state' => 'SP',
                'zip_code' => '00000-000',
                'is_active' => true,
            ]
        );

        // 3. Create a default Team
        Team::updateOrCreate(
            ['slug' => 'sub-17-p'],
            [
                'name' => 'Sub-17 Profissional',
                'category' => 'sub-17',
                'level' => 'advanced',
                'branch_id' => $branch->id,
                'coach_id' => $coach->id,
                'is_active' => true,
            ]
        );

        // 4. Create default Site Settings
        $settings = [
            'site_name' => 'Nexts - ' . tenant('id'),
            'site_description' => 'Clube de Futebol Profissional focado em formação de talentos.',
            'hero_title' => 'Formando Campeões do Futuro',
            'hero_subtitle' => 'Excelência em cada passe, paixão em cada gol.',
            'about_text' => 'O Nexts é mais do que um clube, é uma família dedicada ao desenvolvimento integral de jovens atletas através do esporte.',
            'contact_phone' => '(11) 99999-9999',
            'contact_email' => 'contato@' . tenant('id') . '.com.br',
            'contact_address' => 'Rua do Esporte, 100 - Sede',
            'history_years' => 15,
            'titles_count' => 8,
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'youtube_url' => 'https://youtube.com',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => is_numeric($value) ? 'number' : 'text',
                    'is_public' => true,
                ]
            );
        }
    }
}
