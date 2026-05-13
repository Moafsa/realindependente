<?php
use App\Models\PerformanceRecord;
use App\Models\Athlete;

$tenant = \App\Models\Tenant::find('b82bb310-e7b9-42b2-bd0a-d6b4dc1ac607');
tenancy()->initialize($tenant);

$moacir = Athlete::where('full_name', 'like', '%Moacir%')->first();
if ($moacir) {
    echo "Moacir Records Metrics:\n";
    $metrics = $moacir->performanceRecords()->pluck('metric')->unique()->toArray();
    print_r($metrics);
}

$elias = Athlete::where('full_name', 'like', '%Elias%')->first();
if ($elias) {
    echo "\nElias Records:\n";
    foreach ($elias->performanceRecords as $r) {
        echo "ID: {$r->id}, Metric: {$r->metric}, Value: {$r->value}\n";
    }
}
