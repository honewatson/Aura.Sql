<?php
namespace Aura\Sql\Mapper;

use Aura\Sql\Connection\ConnectionLocator;
use Aura\Sql\DbSetup;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    protected $work;
    
    protected $connections;
    
    protected $mapper;
    
    protected $gateway;
    
    protected $gateways;
    
    protected function setUp()
    {
        $this->markTestSkipped('Skip until we inject QueryFactory instead of Connection.');
        parent::setUp();
        
        $db_setup = new DbSetup\Sqlite;
        
        $this->connections = new ConnectionLocator(
            function () use ($db_setup) { return $db_setup->getConnection(); },
            [],
            []
        );
        
        $this->mapper = new MockMapper;
        
        $this->gateway = new Gateway($this->connections, $this->mapper);
        
        $this->gateways = new GatewayLocator([
            'mock' => function () { return $this->gateway; },
        ]);
        
        $this->work = new UnitOfWork($this->gateways);
        
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testInsert()
    {
        $entity = new MockEntity;
        $entity->firstName = 'Laura';
        $entity->sizeScope = 10;
        $this->work->insert('mock', $entity);
        
        $storage = $this->work->getEntities();
        $this->assertSame(1, count($storage));
        $this->assertTrue($storage->contains($entity));
        
        $expect = ['method' => 'execInsert', 'gateway_name' => 'mock'];
        $actual = $storage[$entity];
        $this->assertSame($expect, $actual);
    }

    public function testUpdate()
    {
        // get the entity
        $select = $this->gateway->newSelect();
        $select->where('name = ?', 'Anna');
        $entity = new MockEntity($this->gateway->fetchOne($select));
        
        // modify it and attach for update
        $entity->firstName = 'Annabelle';
        $this->work->update('mock', $entity);
        
        // get it and see if it's set up right
        $storage = $this->work->getEntities();
        $this->assertSame(1, count($storage));
        $this->assertTrue($storage->contains($entity));
        
        $expect = [
            'method' => 'execUpdate',
            'gateway_name' => 'mock',
            'initial_data' => null
        ];
        $actual = $storage[$entity];
        $this->assertSame($expect, $actual);
    }

    public function testDelete()
    {
        // get the entity
        $select = $this->gateway->newSelect();
        $select->where('name = ?', 'Anna');
        $entity = new MockEntity($this->gateway->fetchOne($select));
        
        // attach for delete
        $this->work->delete('mock', $entity);
        
        // get it and see if it's set up right
        $storage = $this->work->getEntities();
        $this->assertSame(1, count($storage));
        $this->assertTrue($storage->contains($entity));
        
        $expect = ['method' => 'execDelete', 'gateway_name' => 'mock'];
        $actual = $storage[$entity];
        $this->assertSame($expect, $actual);
    }

    public function testDetach()
    {
        // create an entity
        $entity = new MockEntity;
        $entity->firstName = 'Laura';
        $entity->sizeScope = 10;
        
        // attach it
        $this->work->insert('mock', $entity);
        
        // make sure it's attached
        $storage = $this->work->getEntities();
        $this->assertSame(1, count($storage));
        $this->assertTrue($storage->contains($entity));
        $expect = ['method' => 'execInsert', 'gateway_name' => 'mock'];
        $actual = $storage[$entity];
        $this->assertSame($expect, $actual);
        
        // detach it
        $this->work->detach($entity);
        
        // make sure it's detached
        $storage = $this->work->getEntities();
        $this->assertSame(0, count($storage));
    }

    public function testLoadAndGetConnections()
    {
        $this->work->loadConnections();
        $conns = $this->work->getConnections();
        $this->assertTrue($conns->contains($this->connections->getWrite()));
    }

    public function testExec_success()
    {
        // entity collection
        $coll = [];
        
        // insert
        $coll[0] = new MockEntity;
        $coll[0]->firstName = 'Laura';
        $coll[0]->sizeScope = 10;
        $this->work->insert('mock', $coll[0]);
        
        // update
        $select = $this->gateway->newSelect();
        $select->where('name = ?', 'Anna');
        $coll[1] = new MockEntity($this->gateway->fetchOne($select));
        $coll[1]->firstName = 'Annabelle';
        $this->work->update('mock', $coll[1]);
        
        // delete
        $select = $this->gateway->newSelect();
        $select->where('name = ?', 'Betty');
        $coll[2] = new MockEntity($this->gateway->fetchOne($select));
        $this->work->delete('mock', $coll[2]);
        
        // execute
        $result = $this->work->exec();
        $this->assertTrue($result);
        
        // check inserted
        $inserted = $this->work->getInserted();
        $this->assertTrue($inserted->contains($coll[0]));
        $expect = ['last_insert_id' => 11];
        $this->assertEquals($expect, $inserted[$coll[0]]);
        
        // check updated
        $updated = $this->work->getUpdated();
        $this->assertTrue($updated->contains($coll[1]));
        
        // check deleted
        $deleted = $this->work->getDeleted();
        $this->assertTrue($deleted->contains($coll[2]));
    }

    public function testExec_failure()
    {
        // insert without name; this should cause an exception and failure
        $entity = new MockEntity;
        $this->work->insert('mock', $entity);
        
        // execute
        $result = $this->work->exec();
        $this->assertFalse($result);
        
        // get the failed object
        $failed = $this->work->getFailed();
        $this->assertSame($entity, $failed);
        
        // get the exception
        $expect = 'SQLSTATE[23000]: Integrity constraint violation: 19 aura_test_table.name may not be NULL';
        $actual = $this->work->getException()->getMessage();
        $this->assertSame($expect, $actual);
    }
}
