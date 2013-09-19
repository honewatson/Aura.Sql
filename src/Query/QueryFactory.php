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
class QueryFactory implements QueryFactoryInterface
{
    public function newDelete(PdoInterface pdo)
    {
        return new Delete(pdo);
    }
    
    public function newInsert(PdoInterface pdo)
    {
        return new Insert(pdo);
    }
    
    public function newSelect(PdoInterface pdo)
    {
        return new Select(pdo);
    }
    
    public function newUpdate(PdoInterface pdo)
    {
        return new Update(pdo);
    }
}
