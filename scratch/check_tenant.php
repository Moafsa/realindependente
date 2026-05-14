<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenantId = 'e4ec560e-d24e-494e-834d-ab6c02d44ab7';
$tenant = \App\Models\Tenant::find($tenantId);

if ($tenant) {
    echo "Tenant Found: " . $tenant->name . "\n";
    echo "Data Column Content:\n";
    print_r($tenant->data);
    echo "\nAll Attributes:\n";
    print_r($tenant->getAttributes());
} else {
    echo "Tenant NOT found with ID: " . $tenantId . "\n";
    // List all tenants to see what we have
    echo "Available Tenants:\n";
    \App\Models\Tenant::all()->each(function($t) {
        echo "ID: " . $t->id . " - Name: " . $t->name . "\n";
    });
}
