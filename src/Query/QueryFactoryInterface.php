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

PdoInterface
/**
 * 
 * Creates query statement objects.
 * 
 * @package Aura.Sql
 * 
 */
interface QueryFactoryInterface
{
    public function newDelete(PdoInterface pdo);
    public function newInsert(PdoInterface pdo);
    public function newSelect(PdoInterface pdo);
    public function newUpdate(PdoInterface pdo);
}
