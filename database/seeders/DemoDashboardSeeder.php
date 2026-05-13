<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PerformanceRecord;
use App\Models\Team;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Branches
        $branches = [
            ['name' => 'Sede Central', 'address' => 'Rua Principal, 100', 'city' => 'São Paulo'],
            ['name' => 'Centro de Treinamento Norte', 'address' => 'Av. Norte, 500', 'city' => 'São Paulo'],
            ['name' => 'Escola de Futebol Sul', 'address' => 'Rua do Esporte, 25', 'city' => 'São Paulo'],
        ];

        $branchModels = [];
        foreach ($branches as $branch) {
            $branchModels[] = Branch::create([
                'name' => $branch['name'],
                'address' => $branch['address'],
                'contact_info' => ['city' => $branch['city'], 'email' => Str::slug($branch['name']) . '@example.com'],
                'phone' => '(11) 9' . rand(7000, 9999) . '-' . rand(1000, 9999),
                'is_active' => true,
            ]);
        }

        // 2. Create Teams
        $teams = [
            ['name' => 'Sub-13 A', 'category' => 'Sub-13', 'color' => '#3B82F6'],
            ['name' => 'Sub-15 Elite', 'category' => 'Sub-15', 'color' => '#10B981'],
            ['name' => 'Sub-17 Profissional', 'category' => 'Sub-17', 'color' => '#F59E0B'],
            ['name' => 'Feminino Principal', 'category' => 'Feminino', 'color' => '#EC4899'],
            ['name' => 'Masters', 'category' => 'Adulto', 'color' => '#6366F1'],
        ];

        $teamModels = [];
        foreach ($teams as $team) {
            $teamModels[] = Team::create([
                'name' => $team['name'],
                'category' => $team['category'],
                'description' => 'Equipe de alto rendimento da categoria ' . $team['category'],
                'color_primary' => $team['color'],
                'color_secondary' => '#1f2937',
                'is_active' => true,
            ]);
        }

        // 3. Create Products (for Orders)
        $products = [
            ['name' => 'Uniforme Completo', 'price' => 150.00],
            ['name' => 'Mensalidade Clube', 'price' => 250.00],
            ['name' => 'Chuteira Real Profissional', 'price' => 380.00],
            ['name' => 'Garrafa Térmica Ri', 'price' => 45.00],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $productModels[] = Product::create([
                'name' => $p['name'],
                'sku' => 'PROD-' . Str::upper(Str::slug($p['name'])),
                'price' => $p['price'],
                'is_active' => true,
                'type' => 'physical',
            ]);
        }

        // 4. Create Athletes and their data
        $positions = ['Goleiro', 'Zagueiro', 'Lateral', 'Meio-campo', 'Atacante'];
        
        for ($i = 0; $i < 50; $i++) {
            $team = $teamModels[array_rand($teamModels)];
            $branch = $branchModels[array_rand($branchModels)];
            
            $athlete = Athlete::create([
                'full_name' => 'Atleta Demo ' . ($i + 1),
                'birth_date' => Carbon::now()->subYears(rand(10, 20))->subDays(rand(1, 365)),
                'position' => $positions[array_rand($positions)],
                'team_id' => $team->id,
                'branch_id' => $branch->id,
                'is_active' => rand(1, 10) > 1, // 90% active
                'height' => rand(160, 195) / 100,
                'weight' => rand(55, 90),
            ]);

            // Performance Records (Evolution)
            $metrics = ['Velocidade', 'Resistência', 'Técnica', 'Tático'];
            foreach ($metrics as $metric) {
                $baseValue = rand(60, 85);
                // Create 3 months of evolution
                for ($m = 2; $m >= 0; $m--) {
                    PerformanceRecord::create([
                        'athlete_id' => $athlete->id,
                        'metric' => $metric,
                        'value' => $baseValue + (rand(-2, 5)),
                        'recorded_at' => Carbon::now()->subMonths($m)->subDays(rand(1, 28)),
                    ]);
                }
            }

            // Orders (Revenue)
            if (rand(1, 10) > 3) {
                // Last 6 months of revenue
                for ($m = 5; $m >= 0; $m--) {
                    if (rand(1, 10) > 5) {
                        $order = Order::create([
                            'athlete_id' => $athlete->id,
                            'total_amount' => 0,
                            'status' => 'paid',
                            'paid_at' => Carbon::now()->subMonths($m)->subDays(rand(1, 28)),
                            'created_at' => Carbon::now()->subMonths($m)->subDays(rand(1, 28)),
                        ]);

                        $qty = rand(1, 2);
                        $product = $productModels[array_rand($productModels)];
                        
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->price,
                            'total' => $product->price * $qty,
                        ]);

                        $order->update(['total_amount' => $product->price * $qty]);
                    }
                }
            }
        }
    }
}
