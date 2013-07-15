<?php
namespace Aura\Sql\Setup;

class SqlsrvSetup extends AbstractSetup
{
    protected $type = 'Sqlsrv';
    
    protected $create_table = "CREATE TABLE aura_test_table (
         id                     INTEGER PRIMARY KEY AUTOINCREMENT
        ,name                   VARCHAR(50) NOT NULL
        ,test_size_scale        NUMERIC(7,3)
        ,test_default_null      CHAR(3) DEFAULT NULL
        ,test_default_string    VARCHAR(7) DEFAULT 'string'
        ,test_default_number    NUMERIC(5) DEFAULT 12345
        ,test_default_ignore    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
}
