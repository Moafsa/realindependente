<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'elonbettor@gmail.com';
$password = 'password'; // O que eu defini

echo "Testing Central Login for {$email}...\n";
$user = User::where('email', $email)->first();
if ($user) {
    $match = Hash::check($password, $user->password);
    echo "User found in Central. Password match: " . ($match ? "YES" : "NO") . "\n";
    echo "Hash in DB: {$user->password}\n";
} else {
    echo "User NOT found in Central.\n";
}

$tenant = Tenant::where('email', $email)->first();
if ($tenant) {
    echo "Testing Tenant Login for {$email} in tenant {$tenant->id}...\n";
    tenancy()->initialize($tenant);
    $tUser = User::where('email', $email)->first();
    if ($tUser) {
        $match = Hash::check($password, $tUser->password);
        echo "User found in Tenant. Password match: " . ($match ? "YES" : "NO") . "\n";
        echo "Hash in DB: {$tUser->password}\n";
        echo "User ID: {$tUser->id}\n";
    } else {
        echo "User NOT found in Tenant.\n";
    }
    tenancy()->end();
}

echo "Check complete.\n";
