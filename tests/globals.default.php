<?php
/**
 * Mysql
 */
$GLOBALS['Aura\Sql\Setup\MysqlSetup']['connection_params'] = [
    'dsn'           => 'mysql:host=127.0.0.1',
    'username'      => 'root',
    'password'      => '',
    'options'       => [],
    'attributes'    => [],
];

/**
 * Pgsql
 */
$GLOBALS['Aura\Sql\Setup\PgsqlSetup']['connection_params'] = [
    'dsn'           => 'pgsql:host=127.0.0.1;dbname=test',
    'username'      => 'postgres',
    'password'      => '',
    'options'       => [],
    'attributes'    => [],
];

/**
 * Sqlite
 */
$GLOBALS['Aura\Sql\Setup\SqliteSetup']['connection_params'] = [
    'dsn'           => 'sqlite::memory:',
    'username'      => null,
    'password'      => null,
    'options'       => [],
    'attributes'    => [],
];
