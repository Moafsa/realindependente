<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$tenant = Tenant::where('email', 'clerio@gmail.com')->first();
if ($tenant) {
    $tenant->update(['status' => 'active']);
    $tenant->domains()->update(['is_verified' => true]);
    
    tenancy()->initialize($tenant);
    
    User::updateOrCreate(
        ['email' => 'clerio@gmail.com'],
        [
            'name' => 'Clerio Oliveira',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true
        ]
    );
    
    echo "Tenant activated and admin user created successfully.\n";
} else {
    echo "Tenant not found.\n";
}
