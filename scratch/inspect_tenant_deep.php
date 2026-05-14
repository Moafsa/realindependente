<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tenantId = 'e4ec560e-d24e-494e-834d-ab6c02d44ab7';
$tenant = \App\Models\Tenant::find($tenantId);

if ($tenant) {
    echo "Tenant: " . $tenant->name . "\n";
    echo "Data Blob JSON: " . json_encode($tenant->data) . "\n";
    echo "Attributes: " . json_encode($tenant->getAttributes()) . "\n";
} else {
    echo "Tenant not found\n";
}
