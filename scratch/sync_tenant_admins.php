<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "Processing Tenant: {$tenant->id} ({$tenant->email})\n";
    
    try {
        tenancy()->initialize($tenant);
        
        $adminEmail = $tenant->email;
        
        // Verifica se o usuário existe no banco do tenant
        $user = User::where('email', $adminEmail)->first();
        
        if (!$user) {
            echo "Creating admin user for tenant...\n";
            // Tentamos recuperar a senha do banco central se possível, 
            // mas como é Hash, vamos resetar para uma padrão ou a que o usuário costuma usar.
            // O usuário disse que usou suas credenciais, provavelmente a mesma senha.
            // Para segurança, vamos buscar o usuário no banco central e copiar o hash.
            
            // Volta para o central momentaneamente
            tenancy()->end();
            $centralUser = User::where('email', $adminEmail)->first();
            tenancy()->initialize($tenant);
            
            if ($centralUser) {
                User::create([
                    'name' => $centralUser->name,
                    'email' => $adminEmail,
                    'password' => $centralUser->password, // Copia o hash exato
                    'role' => 'admin',
                    'is_active' => true,
                    'is_super_admin' => false,
                ]);
                echo "Admin user created successfully using central password hash.\n";
            } else {
                echo "Central user not found for {$adminEmail}. Skipping.\n";
            }
        } else {
            echo "Admin user already exists.\n";
        }
        
        tenancy()->end();
    } catch (\Exception $e) {
        echo "Error processing tenant {$tenant->id}: " . $e->getMessage() . "\n";
    }
}

echo "Sync completed!\n";
