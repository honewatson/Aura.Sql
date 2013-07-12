<?php
namespace Aura\Sql\Pdo;

class ExtendedPdoTest extends \PHPUnit_Framework_TestCase
{
    protected $pdo;
    
    public function setUp()
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped("Need 'pdo_sqlite' to test in memory.");
        }
        
        $dsn = 'sqlite::memory:';
        $username = null;
        $password = null;
        $driver_options = null;
        $attributes = [ExtendedPdo::ATTR_ERRMODE => ExtendedPdo::ERRMODE_EXCEPTION];
        
        $this->pdo = new ExtendedPdo(
            $dsn,
            $username,
            $password,
            $driver_options,
            $attributes
        );
        
        $this->createTable();
        $this->fillTable();
    }
    
    protected function createTable()
    {
        $statement = "CREATE TABLE pdotest (
            id   INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(10) NOT NULL
        )";
        
        $this->pdo->exec($statement);
    }
    
    // only fills in schema 1
    protected function fillTable()
    {
        $names = [
            'Anna', 'Betty', 'Clara', 'Donna', 'Fiona',
            'Gertrude', 'Hanna', 'Ione', 'Julia', 'Kara',
        ];
        
        foreach ($names as $name) {
            $this->insert(['name' => $name]);
        }
    }
    
    protected function insert(array $data)
    {
        $cols = array_keys($data);
        $vals = [];
        foreach ($cols as $col) {
            $vals[] = ":$col";
        }
        $cols = implode(', ', $cols);
        $vals = implode(', ', $vals);
        $statement = "INSERT INTO pdotest ({$cols}) VALUES ({$vals})";
        $this->pdo->bindValues($data);
        $this->pdo->exec($statement);
    }
    
    public function testQuery()
    {
        $statement = "SELECT * FROM pdotest";
        $sth = $this->pdo->query($statement);
        $this->assertInstanceOf('PDOStatement', $sth);
        $result = $sth->fetchAll(ExtendedPdo::FETCH_ASSOC);
        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
    }
    
    public function testBindValues()
    {
        $expect = ['foo' => 'bar', 'baz' => 'dib'];
        $this->pdo->bindValues($expect);
        $actual = $this->pdo->getBindValues();
        $this->assertSame($expect, $actual);
    }
    
    public function testQueryWithBindValues()
    {
        $statement = "SELECT * FROM pdotest WHERE id <= :val";
        $this->pdo->bindValues(['val' => '5']);
        $sth = $this->pdo->query($statement);
        $this->assertInstanceOf('PDOStatement', $sth);
        $result = $sth->fetchAll(ExtendedPdo::FETCH_ASSOC);
        $expect = 5;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
    }
    
    public function testQueryWithArrayValues()
    {
        $statement = "SELECT * FROM pdotest WHERE id IN (:list) OR id = :id";
        
        $this->pdo->bindValues([
            'list' => [1, 2, 3, 4],
            'id' => 5
        ]);
        
        $sth = $this->pdo->query($statement);
        $this->assertInstanceOf('PDOStatement', $sth);
        
        $result = $sth->fetchAll(ExtendedPdo::FETCH_ASSOC);
        $expect = 5;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
    }
    
    public function testQueryWithFetchMode()
    {
        $statement = "SELECT id, name FROM pdotest";
        
        // mode and 2 args
        $sth = $this->pdo->query($statement, ExtendedPdo::FETCH_CLASS, 'StdClass', []);
        $actual = $sth->fetchAll();
        $expect = [
            0 => (object) [
               'id' => '1',
               'name' => 'Anna',
            ],
            1 => (object) [
               'id' => '2',
               'name' => 'Betty',
            ],
            2 => (object) [
               'id' => '3',
               'name' => 'Clara',
            ],
            3 => (object) [
               'id' => '4',
               'name' => 'Donna',
            ],
            4 => (object) [
               'id' => '5',
               'name' => 'Fiona',
            ],
            5 => (object) [
               'id' => '6',
               'name' => 'Gertrude',
            ],
            6 => (object) [
               'id' => '7',
               'name' => 'Hanna',
            ],
            7 => (object) [
               'id' => '8',
               'name' => 'Ione',
            ],
            8 => (object) [
               'id' => '9',
               'name' => 'Julia',
            ],
            9 => (object) [
               'id' => '10',
               'name' => 'Kara',
            ],
        ];
        $this->assertEquals($expect, $actual);
        
        // mode and 1 arg
        $sth = $this->pdo->query($statement, ExtendedPdo::FETCH_COLUMN, 1);
        $actual = $sth->fetchAll();
        $expect = [
            0 => 'Anna',
            1 => 'Betty',
            2 => 'Clara',
            3 => 'Donna',
            4 => 'Fiona',
            5 => 'Gertrude',
            6 => 'Hanna',
            7 => 'Ione',
            8 => 'Julia',
            9 => 'Kara',
        ];
        $this->assertSame($actual, $expect);
        
        // mode only
        $sth = $this->pdo->query($statement, ExtendedPdo::FETCH_ASSOC);
        $actual = $sth->fetchAll();
        $expect = [
            0 => [
               'id' => '1',
               'name' => 'Anna',
            ],
            1 => [
               'id' => '2',
               'name' => 'Betty',
            ],
            2 => [
               'id' => '3',
               'name' => 'Clara',
            ],
            3 => [
               'id' => '4',
               'name' => 'Donna',
            ],
            4 => [
               'id' => '5',
               'name' => 'Fiona',
            ],
            5 => [
               'id' => '6',
               'name' => 'Gertrude',
            ],
            6 => [
               'id' => '7',
               'name' => 'Hanna',
            ],
            7 => [
               'id' => '8',
               'name' => 'Ione',
            ],
            8 => [
               'id' => '9',
               'name' => 'Julia',
            ],
            9 => [
               'id' => '10',
               'name' => 'Kara',
            ],
        ];
        $this->assertEquals($expect, $actual);
        
    }
    
    public function testPrepareWithQuotedStringsAndData()
    {
        $statement = "SELECT * FROM pdotest
                 WHERE 'leave '':foo'' alone'
                 AND id IN (:list)
                 AND \"leave '':bar' alone\"";
        
        $this->pdo->bindValues([
            'list' => ['1', '2', '3', '4', '5'],
            'foo' => 'WRONG',
            'bar' => 'WRONG',
        ]);
        
        $sth = $this->pdo->prepare($statement);
        
        $expect = str_replace(':list', "'1', '2', '3', '4', '5'", $statement);
        $actual = $sth->queryString;
        $this->assertSame($expect, $actual);
    }
    
    public function testFetchAll()
    {
        $statement = "SELECT * FROM pdotest";
        $result = $this->pdo->fetchAll($statement);
        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchAssoc()
    {
        $statement = "SELECT * FROM pdotest ORDER BY id";
        $result = $this->pdo->fetchAssoc($statement);
        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
        
        // 1-based IDs, not 0-based sequential values
        $expect = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $actual = array_keys($result);
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchCol()
    {
        $statement = "SELECT id FROM pdotest ORDER BY id";
        $result = $this->pdo->fetchCol($statement);
        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
        
        // // 1-based IDs, not 0-based sequential values
        $expect = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
        $this->assertEquals($expect, $result);
    }
    
    public function testFetchOne()
    {
        $statement = "SELECT id, name FROM pdotest WHERE id = 1";
        $actual = $this->pdo->fetchOne($statement);
        $expect = [
            'id'   => '1',
            'name' => 'Anna',
        ];
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchPairs()
    {
        $statement = "SELECT id, name FROM pdotest ORDER BY id";
        $actual = $this->pdo->fetchPairs($statement);
        $expect = [
          1  => 'Anna',
          2  => 'Betty',
          3  => 'Clara',
          4  => 'Donna',
          5  => 'Fiona',
          6  => 'Gertrude',
          7  => 'Hanna',
          8  => 'Ione',
          9  => 'Julia',
          10 => 'Kara',
        ];
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchValue()
    {
        $statement = "SELECT id FROM pdotest WHERE id = 1";
        $actual = $this->pdo->fetchValue($statement);
        $expect = '1';
        $this->assertEquals($expect, $actual);
    }
    
    public function testQuote()
    {
        // quote a string
        $actual = $this->pdo->quote('"foo" bar \'baz\'');
        $this->assertEquals("'\"foo\" bar ''baz'''", $actual);
        
        // quote an integer
        $actual = $this->pdo->quote(123);
        $this->assertEquals("'123'", $actual);
        
        // quote a float
        $actual = $this->pdo->quote(123.456);
        $this->assertEquals("'123.456'", $actual);
        
        // quote an array
        $actual = $this->pdo->quote(['"foo"', 'bar', "'baz'"]);
        $this->assertEquals( "'\"foo\"', 'bar', '''baz'''", $actual);
    }
    
    public function testLastInsertId()
    {
        $cols = ['name' => 'Laura'];
        $this->insert($cols);
        $expect = 11;
        $actual = $this->pdo->lastInsertId();
        $this->assertEquals($expect, $actual);
    }
    
    public function testTransactions()
    {
        // data
        $cols = ['name' => 'Laura'];
    
        // begin and rollback
        $this->assertFalse($this->pdo->inTransaction());
        $this->pdo->beginTransaction();
        $this->assertTrue($this->pdo->inTransaction());
        $this->insert($cols);
        $actual = $this->pdo->fetchAll("SELECT * FROM pdotest");
        $this->assertSame(11, count($actual));
        $this->pdo->rollback();
        $this->assertFalse($this->pdo->inTransaction());
        
        $actual = $this->pdo->fetchAll("SELECT * FROM pdotest");
        $this->assertSame(10, count($actual));
        
        // begin and commit
        $this->assertFalse($this->pdo->inTransaction());
        $this->pdo->beginTransaction();
        $this->assertTrue($this->pdo->inTransaction());
        $this->insert($cols);
        $this->pdo->commit();
        $this->assertFalse($this->pdo->inTransaction());
        
        $actual = $this->pdo->fetchAll("SELECT * FROM pdotest");
        $this->assertSame(11, count($actual));
    }
    
    // public function testAppendLimit()
    // {
    //     $statement = '';
    //     $this->pdo->appendLimit($statement, 10);
    //     $this->assertSame($this->expect_append_limit, trim($statement));
    //     
    //     $statement = '';
    //     $this->pdo->appendLimit($statement, 10, 20);
    //     $this->assertSame($this->expect_append_limit_offset, trim($statement));
    // }
    // 
    // protected function insert($table, array $data)
    // {
    //     $cols = array_keys($data);
    //     $vals = [];
    //     foreach ($cols as $col) {
    //         $vals[] = ":$col";
    //     }
    //     $cols = implode(', ', $cols);
    //     $vals = implode(', ', $vals);
    //     $statement = "INSERT INTO {$table} ({$cols}) VALUES ({$vals})";
    //     $this->pdo->query($statement, $data);
    // }
    // 
    // protected function fetchLastInsertId()
    // {
    //     return $this->pdo->lastInsertId($this->table, 'id');
    // }
}
