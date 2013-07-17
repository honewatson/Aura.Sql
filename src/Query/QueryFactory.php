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

/**
 * 
 * Creates query statement objects.
 * 
 * @package Aura.Sql
 * 
 */
class QueryFactory implements QueryFactoryInterface
{
    public function newDelete(ConnectionInterface $connection)
    {
        return new Delete($connection);
    }
    
    public function newInsert(ConnectionInterface $connection)
    {
        return new Insert($connection);
    }
    
    public function newSelect(ConnectionInterface $connection)
    {
        return new Select($connection);
    }
    
    public function newUpdate(ConnectionInterface $connection)
    {
        return new Update($connection);
    }
}
