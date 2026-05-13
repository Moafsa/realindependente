<?php
use App\Models\PerformanceRecord;
use App\Models\Athlete;

// Try to find any performance records in any tenant
$tenants = \App\Models\Tenant::all();
foreach ($tenants as $tenant) {
    tenancy()->initialize($tenant);
    $count = PerformanceRecord::count();
    echo "Tenant: " . $tenant->id . " - Records: " . $count . "\n";
    if ($count > 0) {
        $latest = PerformanceRecord::latest()->first();
        echo "Latest Record: ID=" . $latest->id . " Metric=" . $latest->metric . " Value=" . $latest->value . " Athlete=" . $latest->athlete_id . " Recorded At=" . $latest->recorded_at . "\n";
        
        $athlete = Athlete::find($latest->athlete_id);
        if ($athlete) {
            echo "Athlete: " . $athlete->full_name . "\n";
        }
    }
    tenancy()->end();
}
