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
namespace Aura\Sql\Connection;

use Aura\Sql\Pdo\ExtendedPdoInterface;

interface ConnectionInterface extends ExtendedPdoInterface
{
    public function lastInsertId($table = null, $col = null);
    public function getLastInsertIdName($table = null, $col =  null);
    public function limit(&$text, $count, $offset = 0);
    public function quoteValuesIn($text, $bind);
    public function quoteName($name);
    public function quoteNamesIn($text);
}
