<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Domain;
use App\Models\Plan;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo tenant
        $plan = Plan::where('slug', 'professional')->first();
        
        if (!$plan) {
            $this->command->error('Professional plan not found. Please run PlanSeeder first.');
            return;
        }

        $tenant = Tenant::create([
            'id' => 'demo-club-001',
            'name' => 'Real Independent Club',
            'email' => 'admin@realindependent.com',
            'domain' => 'demo.localhost',
            'plan_id' => $plan->id,
            'data' => json_encode([
                'club_name' => 'Real Independent Club',
                'founded_year' => 2010,
                'description' => 'Clube de futebol profissional com foco no desenvolvimento de atletas',
                'logo' => '/images/logo.png',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'address' => 'Rua das Flores, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'phone' => '(11) 3456-7890',
                'social_media' => [
                    'instagram' => '@realindependent',
                    'facebook' => 'RealIndependentClub',
                    'twitter' => '@realindependent'
                ]
            ]),
            'is_active' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);

        // Create domain for the tenant
        Domain::create([
            'domain' => 'demo.localhost',
            'tenant_id' => $tenant->id,
            'is_primary' => true,
            'is_verified' => true,
        ]);

        $this->command->info('Demo tenant created successfully!');
    }
}