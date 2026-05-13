<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SiteSetting;
use Stancl\Tenancy\Database\Models\Tenant;

Tenant::all()->runForEach(function ($tenant) {
    echo "Seeding tenant: " . $tenant->id . "\n";
    
    $settings = [
        ['key' => 'site_name', 'value' => 'Nexts Club', 'type' => 'text', 'is_public' => true],
        ['key' => 'site_description', 'value' => 'Sistema de Gestão de Clubes', 'type' => 'text', 'is_public' => true],
        ['key' => 'primary_color', 'value' => '#2563eb', 'type' => 'color', 'is_public' => true],
    ];

    foreach ($settings as $setting) {
        SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
    }
});
