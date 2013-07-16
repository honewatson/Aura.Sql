<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @package Aura.Sql
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Sql\Query;

use Aura\Sql\Connection\ConnectionInterface;
use Aura\Sql\Connection\ConnectionLocatorInterface;

/**
 * 
 * Creates query statement objects.
 * 
 * @package Aura.Sql
 * 
 */
class QueryFactory implements QueryFactoryInterface
{
    public function __construct(ConnectionLocatorInterface $connections)
    {
        $this->connections = $connections;
    }
    
    public function newDelete(ConnectionInterface $connection = null)
    {
        return new Delete($this->getConnection('write', $connection));
    }
    
    public function newInsert(ConnectionInterface $connection = null)
    {
        return new Insert($this->getConnection('write', $connection));
    }
    
    public function newSelect(ConnectionInterface $connection = null)
    {
        return new Select($this->getConnection('read', $connection));
    }
    
    public function newUpdate(ConnectionInterface $connection = null)
    {
        return new Update($this->getConnection('write', $connection));
    }
    
    protected function getConnection($type, $connection)
    {
        if ($connection) {
            return $connection;
        }
        
        if ($type == 'read') {
            return $this->connections->getRead();
        }
        
        if ($type == 'write') {
            return $this->connections->getWrite();
        }
    }
}
