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

use Aura\Sql\Connection\AbstractConnection;

/**
 * 
 * Creates query statement objects.
 * 
 * @package Aura.Sql
 * 
 */
class QueryFactory
{
    public function newDelete(AbstractConnection $connection)
    {
        return new Delete($connection);
    }
    
    public function newInsert(AbstractConnection $connection)
    {
        return new Insert($connection);
    }
    
    public function newSelect(AbstractConnection $connection)
    {
        return new Select($connection);
    }
    
    public function newUpdate(AbstractConnection $connection)
    {
        return new Update($connection);
    }
}
