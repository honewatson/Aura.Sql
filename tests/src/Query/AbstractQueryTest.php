<?php
namespace Aura\Sql\Query;

use Aura\Sql\Assertions;
use Aura\Sql\Pdo\ExtendedPdo;
use Aura\Sql\Profiler;
use Aura\Sql\Query\QueryFactory;
use Aura\Sql\Connection\Sqlite;

abstract class AbstractQueryTest extends \PHPUnit_Framework_TestCase
{
    use Assertions;
    
    protected $query_type;
    
    protected $query;

    protected $connection;
    
    protected function setUp()
    {
        parent::setUp();
        $this->connection = new Sqlite('sqlite::memory:');
        $query_factory = new QueryFactory;
        $method = 'new' . $this->query_type;
        $this->query = $query_factory->$method($this->connection);
    }
    
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetConnection()
    {
        $connection = $this->query->getConnection();
        $this->assertSame($this->connection, $connection);
    }
    
    public function testSetAddGetBind()
    {
        $actual = $this->query->getBindValues();
        $this->assertSame([], $actual);
        
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->query->bindValues($expect);
        $actual = $this->query->getBindValues();
        $this->assertSame($expect, $actual);
        
        $this->query->bindValues(['zim' => 'gir']);
        $expect = ['foo' => 'bar', 'baz' => 'dib', 'zim' => 'gir'];
        $actual = $this->query->getBindValues();
        $this->assertSame($expect, $actual);
    }
}
