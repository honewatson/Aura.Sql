<?php
namespace Aura\Sql\Query;

use Aura\Sql\Assertions;
use Aura\Sql\Pdo\ExtendedPdo;
use Aura\Sql\Profiler;
use Aura\Sql\Query\QueryFactory;
use Aura\Sql\Connection\SqliteConnection;
use Aura\Sql\Connection\ConnectionLocator;

abstract class AbstractQueryTest extends \PHPUnit_Framework_TestCase
{
    use Assertions;
    
    protected $query_type;
    
    protected $query;

    protected $connections;
    
    protected $query_factory;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->connections = new ConnectionLocator(
            function () { return new SqliteConnection('sqlite::memory:'); }
        );
        
        $this->query_factory = new QueryFactory($this->connections);
        
        $method = 'new' . $this->query_type;
        $this->query = $this->query_factory->$method();
    }
    
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetConnection()
    {
        $actual = $this->query->getConnection();
        $expect = $this->connections->getDefault();
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
