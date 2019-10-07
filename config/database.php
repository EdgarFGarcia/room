<?php
    
    // $server = $_SERVER['SERVER_ADDR'];

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |   
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', ''),
            'port' => env('DB_PORT', ''),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ],

        'inspections' => [
            'driver' => 'mysql',
            'host' => '192.168.1.23',
            'port' => '3306',
            'database' => 'inspections',
            'username' => 'sa',
            'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'vcitd' => [
            'driver' => 'mysql',
            'host' => '192.168.1.23',
            'port' => '3306',
            'database' => 'vcitd',
            'username' => 'sa',
            'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'notif' => [
            'driver' => 'mysql',
            'host' => '192.168.1.23',
            'port' => '3306',
            'database' => 'vc_localdata_federated',
            'username' => 'sa',
            'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'hms' => [
            'driver' => 'mysql',
            //'host' => env('DB_HOST2', '127.0.0.1'),
            //'port' => env('DB_PORT2', '3306'),
            //'database' => env('DB_DATABASE2', 'forge'),
            //'username' => env('DB_USERNAME2', 'forge'),cd
            //'password' => env('DB_PASSWORD2', ''),
    	    'host' => env('DB_HOST', ''),
    	    'port' => '3306',
    	    'database' => 'hms',
    	    'username' => 'sa',
    	    'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'backup' => [
            'driver' => 'mysql',
    	    'host' => '192.168.1.90',
    	    'port' => '3306',
    	    'database' => 'rmsback',
    	    'username' => 'sa',
    	    'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'dataanalytics' => [
            'driver' => 'mysql',
    	    'host' => '192.168.1.90',
    	    'port' => '3306',
    	    'database' => 'vc_autopilot_reports_analytics',
    	    'username' => 'sa',
    	    'password' => '1tdAutop1l0t',
            'unix_socket' => env('DB_SOCKET'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        // VCHI 13.12
        // 'hms' => [
        //     'driver' => 'mysql',
        //     //'host' => env('DB_HOST2', '127.0.0.1'),
        //     //'port' => env('DB_PORT2', '3306'),
        //     //'database' => env('DB_DATABASE2', 'forge'),
        //     //'username' => env('DB_USERNAME2', 'forge'),cd
        //     //'password' => env('DB_PASSWORD2', ''),
    	//     'host' => '192.168.13.12',
        //     // 'host' => '.$server.',
    	//     'port' => '3306',
    	//     'database' => 'hms',
    	//     'username' => 'sa',
    	//     'password' => '1tdAutop1l0t',
        //     'unix_socket' => env('DB_SOCKET'),
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'strict' => true,
        //     'engine' => null,
        // ],

        // VCMA 10.12
        // 'hms' => [
        //     'driver' => 'mysql',
        //     //'host' => env('DB_HOST2', '127.0.0.1'),
        //     //'port' => env('DB_PORT2', '3306'),
        //     //'database' => env('DB_DATABASE2', 'forge'),
        //     //'username' => env('DB_USERNAME2', 'forge'),cd
        //     //'password' => env('DB_PASSWORD2', ''),
    	//     'host' => '192.168.10.12',
    	//     'port' => '3306',
    	//     'database' => 'hms',
    	//     'username' => 'sa',
    	//     'password' => '1tdAutop1l0t',
        //     'unix_socket' => env('DB_SOCKET'),
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'strict' => true,
        //     'engine' => null,
        // ],

        //VCPA 15.12
        // 'hms' => [
        //     'driver' => 'mysql',
        //     //'host' => env('DB_HOST2', '127.0.0.1'),
        //     //'port' => env('DB_PORT2', '3306'),
        //     //'database' => env('DB_DATABASE2', 'forge'),
        //     //'username' => env('DB_USERNAME2', 'forge'),cd
        //     //'password' => env('DB_PASSWORD2', ''),
    	//     'host' => '192.168.15.12',
    	//     'port' => '3306',
    	//     'database' => 'hms',
    	//     'username' => 'sa',
    	//     'password' => '1tdAutop1l0t',
        //     'unix_socket' => env('DB_SOCKET'),
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'strict' => true,
        //     'engine' => null,
        // ],

        //VCSF
        // 'hms' => [
        //     'driver' => 'mysql',
        //     //'host' => env('DB_HOST2', '127.0.0.1'),
        //     //'port' => env('DB_PORT2', '3306'),
        //     //'database' => env('DB_DATABASE2', 'forge'),
        //     //'username' => env('DB_USERNAME2', 'forge'),cd
        //     //'password' => env('DB_PASSWORD2', ''),
    	//  'host' => '192.168.9.12',
    	//  'port' => '3306',
    	//  'database' => 'hms',
    	//  'username' => 'sa',
    	//  'password' => '1tdAutop1l0t',
        //  'unix_socket' => env('DB_SOCKET'),
        //  'charset' => 'utf8mb4',
        //  'collation' => 'utf8mb4_unicode_ci',
        //  'prefix' => '',
        //  'strict' => true,
        //  'engine' => null,
        // ],

        //VCBA
        // 'hms' => [
        //     'driver' => 'mysql',
        //     //'host' => env('DB_HOST2', '127.0.0.1'),
        //     //'port' => env('DB_PORT2', '3306'),
        //     //'database' => env('DB_DATABASE2', 'forge'),
        //     //'username' => env('DB_USERNAME2', 'forge'),cd
        //     //'password' => env('DB_PASSWORD2', ''),
    	//  'host' => '192.168.3.12',
    	//  'port' => '3306',
    	//  'database' => 'hms',
    	//  'username' => 'sa',
    	//  'password' => '1tdAutop1l0t',
        //  'unix_socket' => env('DB_SOCKET'),
        //  'charset' => 'utf8mb4',
        //  'collation' => 'utf8mb4_unicode_ci',
        //  'prefix' => '',
        //  'strict' => true,
        //  'engine' => null,
        // ],

        'vcreserve' => [
            'driver' => 'mysql',
            'host' => 'external-db.s161964.gridserver.com',
            'port' => env('DB_PORT2', '3306'),
            'database' => 'db161964_vc_reserve',
            'username' => 'db161964',
            'password' => 'king143vc',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        // 'vcreserve' => [
        //     'driver' => 'mysql',
        //     'host' => '192.168.1.8',
        //     'port' => env('DB_PORT2', '3306'),
        //     'database' => 'db161964_vc_reserve',
        //     'username' => 'sa',
        //     'password' => '1tdAutop1l0t',
        //     'unix_socket' => '',
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'strict' => true,
        //     'engine' => null,
        // ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
