<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Stancl\Tenancy\Facades\Tenancy;

// Initialize tenancy for a demo tenant
$tenant = \App\Models\Tenant::first();
if (!$tenant) {
    echo "No tenant found\n";
    exit(1);
}

tenancy()->initialize($tenant);

try {
    $product = Product::create([
        'name' => 'Test Product ' . time(),
        'price' => 10.00,
        'type' => 'product',
        // stock_quantity is omitted, should default to 0 in DB or be null (allowed)
    ]);
    echo "Product created successfully with ID: " . $product->id . " and stock: " . ($product->stock_quantity ?? 'NULL') . "\n";
    $product->delete();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
