<?php
namespace Aura\Sql\DbSetup;

use Aura\Sql\Profiler;
use Aura\Sql\Query\Factory as QueryFactory;

abstract class AbstractDbSetup
{
    protected $extension;
    
    protected $connection;
    
    protected $table = 'aura_test_table';
    
    protected $schema1 = 'aura_test_schema1';
    
    protected $schema2 = 'aura_test_schema2';
    
    protected $create_table;
    
    public function __construct()
    {
        $type = strrchr(get_class($this), '\\');
        $setup_class = 'Aura\Sql\DbSetup' . $type;
        $connection_params = $GLOBALS[$setup_class]['connection_params'];
        
        $connection_class = 'Aura\Sql\Connection' . $type;
        $this->connection = new $connection_class(
            new Profiler,
            new QueryFactory,
            $connection_params['dsn'],
            $connection_params['username'],
            $connection_params['password'],
            $connection_params['options']
        );
        
        $this->exec();
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function getSchema1()
    {
        return $this->schema1;
    }
    
    public function getSchema2()
    {
        return $this->schema2;
    }
    
    public function exec()
    {
        $this->dropSchemas();
        $this->createSchemas();
        $this->createTables();
        $this->fillTable();
    }
    
    abstract protected function createSchemas();
    
    abstract protected function dropSchemas();
    
    protected function createTables()
    {
        // create in schema 1
        $this->connection->query($this->create_table);
        
        // create again in schema 2
        $create_table2 = str_replace(
            $this->table,
            "{$this->schema2}.{$this->table}",
            $this->create_table
        );
        $this->connection->query($create_table2);
    }
    
    // only fills in schema 1
    protected function fillTable()
    {
        $names = [
            'Anna', 'Betty', 'Clara', 'Donna', 'Fiona',
            'Gertrude', 'Hanna', 'Ione', 'Julia', 'Kara',
        ];
        
        foreach ($names as $name) {
            $this->connection->insert($this->table, ['name' => $name]);
        }
    }
}
