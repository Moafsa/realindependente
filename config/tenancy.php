<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | These are the domains that are used for the central application.
    | The central application is where you manage tenants, billing, etc.
    |
    */

    'central_domains' => [
        env('TENANCY_CENTRAL_DOMAIN', 'meuclube.app'),
        'localhost',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | This is the model that will be used to represent tenants in your
    | application. You can use any model you want, but it should implement
    | the Stancl\Tenancy\Contracts\Tenant interface.
    |
    */

    'tenant_model' => App\Models\Tenant::class,

    /*
    |--------------------------------------------------------------------------
    | Domain Model
    |--------------------------------------------------------------------------
    |
    | This is the model that will be used to represent domains in your
    | application. You can use any model you want, but it should implement
    | the Stancl\Tenancy\Contracts\Domain interface.
    |
    */

    'domain_model' => App\Models\Domain::class,

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | This is the database configuration for tenants. You can use any
    | database driver you want, but PostgreSQL is recommended for
    | multi-tenancy applications.
    |
    */

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'pgsql'),
        'template_tenant_connection' => env('DB_CONNECTION', 'pgsql'),
        'prefix_base' => 'tenant_',
        'suffix_base' => '',
        'managers' => [
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | This is the cache configuration for tenants. You can use any
    | cache driver you want, but Redis is recommended for
    | multi-tenancy applications.
    |
    */

    'cache' => [
        'tag_base' => 'tenant',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Configuration
    |--------------------------------------------------------------------------
    |
    | This is the filesystem configuration for tenants. You can use any
    | filesystem driver you want, but S3 is recommended for
    | multi-tenancy applications.
    |
    */

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
            's3',
        ],
        'root_override' => [
            'local' => '%storage_path%/app',
            'public' => '%storage_path%/app/public',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | This is the features configuration for tenants. You can enable
    | or disable features as needed.
    |
    */

    'features' => [
        Stancl\Tenancy\Features\UserImpersonation::class,
        Stancl\Tenancy\Features\TelescopeTags::class,
        Stancl\Tenancy\Features\UniversalRoutes::class,
        Stancl\Tenancy\Features\TenantConfig::class,
        Stancl\Tenancy\Features\CrossDomainRedirect::class,
        Stancl\Tenancy\Features\ViteBundler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bootstrap
    |--------------------------------------------------------------------------
    |
    | This is the bootstrap configuration for tenants. You can customize
    | the bootstrap process as needed.
    |
    */

    'bootstrap' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Parameters
    |--------------------------------------------------------------------------
    |
    | This is the migration parameters for tenants. You can customize
    | the migration process as needed.
    |
    */

    'migration_parameters' => [
        '--path' => database_path('migrations/tenant'),
        '--realpath' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeder Parameters
    |--------------------------------------------------------------------------
    |
    | This is the seeder parameters for tenants. You can customize
    | the seeding process as needed.
    |
    */

    'seeder_parameters' => [
        '--class' => 'Database\Seeders\Tenant\TenantDatabaseSeeder',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Parameters
    |--------------------------------------------------------------------------
    |
    | This is the queue parameters for tenants. You can customize
    | the queue process as needed.
    |
    */

    'queue_parameters' => [
        '--queue' => 'tenant',
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    |
    | This is the maintenance mode configuration for tenants. You can
    | customize the maintenance mode process as needed.
    |
    */

    'maintenance_mode' => [
        'enabled' => env('TENANCY_MAINTENANCE_MODE', false),
        'allowed_ips' => [
            '127.0.0.1',
            '::1',
        ],
    ],

];
