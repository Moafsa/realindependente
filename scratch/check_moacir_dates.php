<?php
use App\Models\PerformanceRecord;
use App\Models\Athlete;

$tenant = \App\Models\Tenant::find('b82bb310-e7b9-42b2-bd0a-d6b4dc1ac607');
tenancy()->initialize($tenant);

$moacir = Athlete::where('full_name', 'like', '%Moacir%')->first();
if ($moacir) {
    echo "Moacir Latest Records:\n";
    foreach ($moacir->performanceRecords()->latest('recorded_at')->take(5)->get() as $r) {
        echo "ID: {$r->id}, Metric: {$r->metric}, Value: {$r->value}, Recorded At: {$r->recorded_at}\n";
    }
}
