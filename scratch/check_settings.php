<?php
require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Tenant::where('email', 'clerio@gmail.com')->first();
if (!$t) {
    echo "Tenant not found\n";
    exit;
}
tenancy()->initialize($t);
echo "Logo: ".App\Models\SiteSetting::where('key', 'site_logo')->first()?->value . "\n";
echo "Banner: ".App\Models\SiteSetting::where('key', 'banner_image')->first()?->value . "\n";
echo "Primary Color: ".App\Models\SiteSetting::where('key', 'color_primary')->first()?->value . "\n";
