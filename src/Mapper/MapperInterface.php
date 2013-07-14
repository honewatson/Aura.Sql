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
namespace Aura\Sql\Mapper;

use Aura\Sql\Query\Delete;
use Aura\Sql\Query\Insert;
use Aura\Sql\Query\Select;
use Aura\Sql\Query\Update;

interface MapperInterface
{
    public function getCols();
    public function getColForField($field);
    public function getFields();
    public function getFieldForCol($col);
    public function getIdentityField();
    public function getIdentityValue($entity);
    public function getPrimaryCol();
    public function getTable();
    public function getTableCol($col);
    public function getTableColAsField($col);
    public function getTablePrimaryCol();
    public function getTableColsAsFields($cols);
    public function modifySelect(Select $select, array $cols = []);
    public function modifyInsert(Insert $insert, $entity);
    public function modifyUpdate(Update $update, $entity, $initial_data = null);
    public function modifyDelete(Delete $delete, $entity);
    public function getInsertData($entity);
    public function getUpdateData($entity, $initial_data = null);
    public function getUpdateDataChanges($entity, $initial_data);
    public function compare($new, $old);
}
