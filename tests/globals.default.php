<?php
/**
 * Mysql
 */

// setup
$GLOBALS['Aura\Sql\DbSetup\Mysql']['connection_params'] = [
    'dsn' => [
        'host' => '127.0.0.1',
    ],
    'username' => 'root',
    'password' => '',
    'options' => [],
];

// test
$GLOBALS['Aura\Sql\Connection\MysqlTest']['expect_dsn_string'] = 'mysql:host=127.0.0.1';

/**
 * Pgsql
 */

// setup
$GLOBALS['Aura\Sql\DbSetup\Pgsql']['connection_params'] = [
    'dsn' => [
        'host' => '127.0.0.1',
        'dbname' => 'test',
    ],
    'username' => 'postgres',
    'password' => '',
    'options' => [],
];

// test
$GLOBALS['Aura\Sql\Connection\PgsqlTest']['expect_dsn_string'] = 'pgsql:host=127.0.0.1;dbname=test';

/**
 * Sqlite
 */
$GLOBALS['Aura\Sql\DbSetup\Sqlite']['connection_params'] = [
    'dsn' => ':memory:',
    'username' => null,
    'password' => null,
    'options' => [],
];

// test
$GLOBALS['Aura\Sql\Connection\SqliteTest']['expect_dsn_string'] = 'sqlite::memory:';
