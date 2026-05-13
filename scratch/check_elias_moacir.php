<?php
use App\Models\PerformanceRecord;
use App\Models\Athlete;

$tenants = \App\Models\Tenant::all();
foreach ($tenants as $tenant) {
    tenancy()->initialize($tenant);
    
    echo "Tenant: " . $tenant->id . "\n";
    
    $elias = Athlete::where('full_name', 'like', '%Elias%')->first();
    if ($elias) {
        echo "Elias Ferreira (ID: {$elias->id}) - Records: " . $elias->performanceRecords()->count() . "\n";
    }
    
    $moacir = Athlete::where('full_name', 'like', '%Moacir%')->first();
    if ($moacir) {
        echo "Moacir (ID: {$moacir->id}) - Records: " . $moacir->performanceRecords()->count() . "\n";
    }
    
    tenancy()->end();
}
