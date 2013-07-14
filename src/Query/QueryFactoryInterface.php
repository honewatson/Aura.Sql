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
interface QueryFactoryInterface
{
    public function newDelete(ConnectionInterface $connection);
    public function newInsert(ConnectionInterface $connection);
    public function newSelect(ConnectionInterface $connection);
    public function newUpdate(ConnectionInterface $connection);
}
