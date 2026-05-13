<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'elonbettor@gmail.com';
$password = 'password';
$hash = Hash::make($password);

echo "Updating password for {$email} in central...\n";
$centralUser = User::where('email', $email)->first();
if ($centralUser) {
    $centralUser->password = $hash;
    $centralUser->save();
    echo "Central password updated.\n";
}

$tenant = Tenant::where('email', $email)->first();
if ($tenant) {
    echo "Updating password for {$email} in tenant {$tenant->id}...\n";
    tenancy()->initialize($tenant);
    $tenantUser = User::where('email', $email)->first();
    if ($tenantUser) {
        $tenantUser->password = $hash;
        $tenantUser->save();
        echo "Tenant password updated.\n";
    }
    tenancy()->end();
}

echo "Reset complete.\n";
