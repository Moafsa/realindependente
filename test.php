<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tenant = new App\Models\Tenant([
    'id' => 'test_conn_' . uniqid(),
    'name' => 'Conn Test',
    'email' => 'conn' . uniqid() . '@test.com',
    'domain' => uniqid() . 'conn.localhost'
]);

var_dump("template_connection value", $tenant->getInternal('template_connection'));
var_dump("config template_tenant_connection", config('tenancy.database.template_tenant_connection'));
var_dump("config central_connection", config('tenancy.database.central_connection'));

try {
    $config = new \Stancl\Tenancy\Database\DatabaseConfig($tenant);
    var_dump("Resolved template name", $config->getTemplateConnectionName());
    
    $driver = config("database.connections.{$config->getTemplateConnectionName()}.driver");
    var_dump("Resolved driver", $driver);
    
} catch(Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
