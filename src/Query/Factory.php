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
class Factory
{
    public function newDelete(AbstractConnection $connection)
    {
        return $this->newInstance('Delete', $connection);
    }
    
    public function newInsert(AbstractConnection $connection)
    {
        return $this->newInstance('Insert', $connection);
    }
    
    public function newSelect(AbstractConnection $connection)
    {
        return $this->newInstance('Select', $connection);
    }
    
    public function newUpdate(AbstractConnection $connection)
    {
        return $this->newInstance('Update', $connection);
    }
    
    /**
     * 
     * Returns a new query object.
     * 
     * @param string $type The query object type.
     * 
     * @param AbstractConnection $connection The SQL connection.
     * 
     * @return AbstractQuery
     * 
     */
    public function newInstance($type, AbstractConnection $connection)
    {
        $class = '\Aura\Sql\Query\\' . ucfirst($type);
        return new $class($connection);
    }
}
