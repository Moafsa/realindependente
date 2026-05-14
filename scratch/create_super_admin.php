<?php
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::updateOrCreate(
    ['email' => 'superadmin@nexts.com'],
    [
        'name' => 'Super Admin',
        'password' => Hash::make('admin'),
        'role' => 'admin',
        'is_active' => true,
        'is_super_admin' => true,
    ]
);

echo "Super Admin created successfully.\n";
