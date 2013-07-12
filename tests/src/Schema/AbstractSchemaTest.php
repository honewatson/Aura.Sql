<?php
namespace Aura\Sql\Schema;

abstract class AbstractSchemaTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;
    
    protected $connection_type;
    
    protected $schema;
    
    protected $expect_fetch_table_list;
    
    protected $expect_fetch_table_cols;
    
    public function setUp()
    {
        // skip if we don't have the extension
        if (! extension_loaded($this->extension)) {
            $this->markTestSkipped("Extension '{$this->extension}' not loaded.");
        }
        
        // convert column arrays to objects
        foreach ($this->expect_fetch_table_cols as $name => $info) {
            $this->expect_fetch_table_cols[$name] = new Column(
                $info['name'],
                $info['type'],
                $info['size'],
                $info['scale'],
                $info['notnull'],
                $info['default'],
                $info['autoinc'],
                $info['primary']
            );
        }
        
        // database setup
        $db_setup_class = 'Aura\Sql\DbSetup\\' . ucfirst($this->connection_type);
        $this->db_setup = new $db_setup_class;
        
        // schema class same as this class, minus "Test"
        $class = substr(get_class($this), 0, -4);
        $this->schema = new $class(
            $this->db_setup->getConnection(),
            new ColumnFactory
        );
    }
    
    public function testGetColumnFactory()
    {
        $actual = $this->schema->getColumnFactory();
        $this->assertInstanceOf('\Aura\Sql\Schema\ColumnFactory', $actual);
    }
    
    public function testFetchTableList()
    {
        $actual = $this->schema->fetchTableList();
        $this->assertEquals($this->expect_fetch_table_list, $actual);
    }
    
    public function testFetchTableList_schema()
    {
        $schema2 = $this->db_setup->getSchema2();
        $actual = $this->schema->fetchTableList($schema2);
        $this->assertEquals($this->expect_fetch_table_list_schema, $actual);
    }
    
    public function testFetchTableCols()
    {
        $table  = $this->db_setup->getTable();
        $actual = $this->schema->fetchTableCols($table);
        $expect = $this->expect_fetch_table_cols;
        ksort($actual);
        ksort($expect);
        $this->assertSame(count($expect), count($actual));
        foreach (array_keys($expect) as $name) {
            $this->assertEquals($expect[$name], $actual[$name]);
        }
    }
    
    public function testFetchTableCols_schema()
    {
        $table  = $this->db_setup->getTable();
        $schema2 = $this->db_setup->getSchema2();
        $actual = $this->schema->fetchTableCols("{$schema2}.{$table}");
        $expect = $this->expect_fetch_table_cols;
        ksort($actual);
        ksort($expect);
        $this->assertSame(count($expect), count($actual));
        foreach ($expect as $name => $info) {
            $this->assertEquals($expect[$name], $actual[$name]);
        }
    }
}
