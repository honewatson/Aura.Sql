<?php
/**
 * Mysql
 */
$GLOBALS['Aura\Sql\DbSetup\Mysql']['connection_params'] = [
    'mysql:host=127.0.0.1',
    'username' => 'root',
    'password' => '',
    'options' => [],
    'attributes' => [],
];

/**
 * Pgsql
 */
$GLOBALS['Aura\Sql\DbSetup\Pgsql']['connection_params'] = [
    'pgsql:host=127.0.0.1;dbname=test',
    'username' => 'postgres',
    'password' => '',
    'options' => [],
    'attributes' => [],
];

/**
 * Sqlite
 */
$GLOBALS['Aura\Sql\DbSetup\Sqlite']['connection_params'] = [
    'dsn' => 'sqlite::memory:',
    'username' => null,
    'password' => null,
    'options' => [],
    'attributes' => [],
];
