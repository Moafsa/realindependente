<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PerformanceRecord;
use App\Models\Athlete;

$athleteId = 6;
$athlete = Athlete::find($athleteId);

if (!$athlete) {
    echo "Athlete $athleteId not found\n";
    exit;
}

echo "Athlete: " . $athlete->full_name . "\n";
echo "Total Records: " . $athlete->performanceRecords()->count() . "\n";

foreach ($athlete->performanceRecords as $record) {
    echo "- Metric: " . $record->metric . ", Value: " . $record->value . ", Date: " . $record->recorded_at . "\n";
}
