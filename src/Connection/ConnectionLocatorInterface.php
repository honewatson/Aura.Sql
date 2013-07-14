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

/**
 * 
 * Manages connections to default, read, and write databases.
 * 
 * @package Aura.Sql
 * 
 */
interface ConnectionLocatorInterface
{
    public function setDefault($spec);
    public function getDefault();
    public function setRead($name, $spec);
    public function getRead($name = null);
    public function setWrite($name, $spec);
    public function getWrite($name = null);
}
