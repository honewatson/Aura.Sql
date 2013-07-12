<?php
namespace Aura\Sql\Mapper;

use Aura\Sql\Assertions;
use Aura\Sql\Connection\ConnectionLocator;
use Aura\Sql\DbSetup;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    use Assertions;

    protected $gateway;

    protected $mapper;
    
    protected $connections;
    
    protected function setUp()
    {
        parent::setUp();
        $db_setup = new DbSetup\Sqlite;
        
        $this->connections = new ConnectionLocator(
            function () use ($db_setup) { return $db_setup->getConnection(); },
            [],
            []
        );
        
        $this->mapper = new MockMapper;
        $this->gateway = new Gateway($this->connections, $this->mapper);
        
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testGetConnections()
    {
        $actual = $this->gateway->getConnections();
        $this->assertSame($this->connections, $actual);
    }

    public function testGetMapper()
    {
        $actual = $this->gateway->getMapper();
        $this->assertSame($this->mapper, $actual);
    }

    // when mapping, add an "if isset()" so that the object does not need
    // all the columns?
    public function testInsertAndLastInsertId()
    {
        $object = (object) [
            'identity' => null,
            'firstName' => 'Laura',
            'sizeScale' => 10,
            'defaultNull' => null,
            'defaultString' => null,
            'defaultNumber' => null,
            'defaultIgnore' => null,
        ];
        
        // do the insert and retain last insert id
        $last_insert_id = $this->gateway->insert($object);
        
        // did we get the right last ID?
        $expect = '11';
        $this->assertEquals($expect, $last_insert_id);
        
        // did it insert?
        $select = $this->gateway->newSelect(['id', 'name'])->where('id = ?', 11);
        $actual = $this->gateway->fetchOne($select);
        $expect = ['identity' => '11', 'firstName' => 'Laura'];
        $this->assertEquals($actual, $expect);
    }
    
    protected function fetchLastInsertId()
    {
        return $this->connections->getWrite()->lastInsertId();
    }
    
    public function testUpdate()
    {
        // select an object ...
        $select = $this->gateway->newSelect()->where('name = ?', 'Anna');
        $object = (object) $this->gateway->fetchOne($select);
        
        // ... then modify and update it.
        $object->firstName = 'Annabelle';
        $this->gateway->update($object);
        
        // did it update?
        $select = $this->gateway->newSelect()->where('name = ?', 'Annabelle');
        $actual = (object) $this->gateway->fetchOne($select);
        $this->assertEquals($actual, $object);
        
        // did anything else update?
        $select = $this->gateway->newSelect(['id', 'name'])->where('id = ?', 2);
        $actual = $this->gateway->fetchOne($select);
        $expect = ['identity' => '2', 'firstName' => 'Betty'];
        $this->assertEquals($actual, $expect);
    }
    
    public function testDelete()
    {
        // select an object ...
        $select = $this->gateway->newSelect()->where('name = ?', 'Anna');
        $object = (object) $this->gateway->fetchOne($select);
        
        // then delete it.
        $this->gateway->delete($object);
        
        // did it delete?
        $select = $this->gateway->newSelect()->where('name = ?', 'Anna');
        $actual = $this->gateway->fetchOne($select);
        $this->assertFalse($actual);
        
        // do we still have everything else?
        $select = $this->gateway->newSelect();
        $actual = $this->gateway->fetchAll($select);
        $expect = 9;
        $this->assertEquals($expect, count($actual));
    }

    public function testNewSelect()
    {
        $select = $this->gateway->newSelect();
        $connection = $select->getConnection();
        $this->assertSame($this->connections->getRead(), $connection);
        $expect = '
            SELECT
                "aura_test_table"."id" AS "identity",
                "aura_test_table"."name" AS "firstName",
                "aura_test_table"."test_size_scale" AS "sizeScale",
                "aura_test_table"."test_default_null" AS "defaultNull",
                "aura_test_table"."test_default_string" AS "defaultString",
                "aura_test_table"."test_default_number" AS "defaultNumber",
                "aura_test_table"."test_default_ignore" AS "defaultIgnore"
            FROM
                "aura_test_table"
        ';
        $actual = (string) $select;
        $this->assertSameSql($expect, $actual);
    }

    public function testFetchAll()
    {
        $select = $this->gateway->newSelect();
        $result = $this->gateway->fetchAll($select);
        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
    }

    public function testFetchCol()
    {
        $select = $this->gateway->newSelect(['id'])->orderBy(['id']);
        $result = $this->gateway->fetchCol($select);

        $expect = 10;
        $actual = count($result);
        $this->assertEquals($expect, $actual);
        
        // // 1-based IDs, not 0-based sequential values
        $expect = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
        $this->assertEquals($expect, $result);
    }

    public function testFetchOne()
    {
        $select = $this->gateway->newSelect(['id', 'name'])->where('id = ?', 1);
        $actual = $this->gateway->fetchOne($select);
        $expect = [
            'identity'  => '1',
            'firstName' => 'Anna',
        ];
        $this->assertEquals($expect, $actual);
    }

    public function testFetchPairs()
    {
        $select = $this->gateway->newSelect(['id', 'name'])->orderBy(['id']);
        $actual = $this->gateway->fetchPairs($select);
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
        $select = $this->gateway->newSelect(['id'])->where('id = ?', 1);
        $actual = $this->gateway->fetchValue($select);
        $expect = '1';
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchOneBy()
    {
        $actual = $this->gateway->fetchOneBy('id', 1);
        unset($actual['defaultIgnore']); // creation date-time
        $expect = [
            'identity' => '1',
            'firstName' => 'Anna',
            'sizeScale' => null,
            'defaultNull' => null,
            'defaultString' => 'string',
            'defaultNumber' => '12345',
        ];
        $this->assertEquals($expect, $actual);
    }
    
    public function testFetchAllBy()
    {
        $actual = $this->gateway->fetchAllBy('id', [1]);
        unset($actual[0]['defaultIgnore']); // creation date-time
        $expect = [
            [
                'identity' => '1',
                'firstName' => 'Anna',
                'sizeScale' => null,
                'defaultNull' => null,
                'defaultString' => 'string',
                'defaultNumber' => '12345',
            ],
        ];
        $this->assertEquals($expect, $actual);
    }
}
