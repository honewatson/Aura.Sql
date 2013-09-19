<?php
namespace Aura\Sql\Query;

use Aura\Sql\Assertions;
use Aura\Sql\SqliteConnection;
use Aura\Sql\Pdo\Pdo;
use Aura\Sql\Profiler;
use Aura\Sql\Query\QueryFactory;

abstract class AbstractQueryTest extends \PHPUnit_Framework_TestCase
{
    use Assertions;
    
    protected $query_type;
    
    protected $query;

    protected $connection;
    
    protected $query_factory;
    
    protected function setUp()
    {
        parent::setUp();
        $this->connection = new SqliteConnection('sqlite::memory:');
        $this->query_factory = new QueryFactory;
        $method = 'new' . $this->query_type;
        $this->query = $this->query_factory->$method($this->connection);
    }
    
    public function testGetConnection()
    {
        $actual = $this->connection;
        $expect = $this->connection;
        $this->assertSame($expect, $actual);
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
