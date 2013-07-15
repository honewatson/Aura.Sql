<?php
namespace Aura\Sql\Connection;
use PDO;

class SqlsrvConnectionTest extends AbstractConnectionTest
{
    protected $extension = 'pdo_sqlsrv';
    
    protected $connection_type = 'sqlsrv';
}
