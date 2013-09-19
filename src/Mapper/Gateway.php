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

use Aura\Sql\PdoLocatorInterface;
use Aura\Sql\Query\QueryFactoryInterface;

/**
 * 
 * A TableDataGateway implementation.
 * 
 * @package Aura.Sql
 * 
 */
class Gateway
{
    /**
     * 
     * A PdoLocator for database connections.
     * 
     * @var PdoLocatorInterface
     * 
     */
    protected $connections;

    /**
     * 
     * A mapper between this table gateway and entities.
     * 
     * @var MapperInterface
     * 
     */
    protected $mapper;

    /**
     * 
     * Constructor.
     * 
     * @param PdoLocatorInterface $connections A PdoLocator for database
     * connections.
     * 
     * @param MapperInterface $mapper A table-to-entity mapper.
     * 
     */
    public function __construct(
        PdoLocatorInterface $connections,
        QueryFactoryInterface $query_factory,
        MapperInterface $mapper
    ) {
        $this->connections = $connections;
        $this->query_factory = $query_factory;
        $this->mapper = $mapper;
    }

    /**
     * 
     * Gets the connection locator.
     * 
     * @return PdoLocatorInterface
     * 
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * 
     * Gets the query factory.
     * 
     * @return QueryFactoryInterface
     * 
     */
    public function getQueryFactory()
    {
        return $this->query_factory;
    }

    /**
     * 
     * Gets the mapper.
     * 
     * @return MapperInterface
     * 
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * 
     * Inserts an entity into the mapped table using a write connection.
     * 
     * @param object $entity The entity to insert.
     * 
     * @return int The last insert ID.
     * 
     */
    public function insert($entity)
    {
        $connection = $this->connections->getWrite();
        $insert = $this->query_factory->newInsert($connection);
        $this->mapper->modifyInsert($insert, $entity);
        $insert->exec();
        return $insert->lastInsertId($this->mapper->getPrimaryCol());
    }

    /**
     * 
     * Updates an entity in the mapped table using a write connection; if an
     * array of initial data is present, updates only changed values.
     * 
     * @param object $entity The entity to update.
     * 
     * @param array $initial_data Initial data for the entity.
     * 
     * @return bool True if the update succeeded, false if not.  (This is
     * determined by checking the number of rows affected by the query.)
     * 
     */
    public function update($entity, $initial_data = null)
    {
        $connection = $this->connections->getWrite();
        $update = $this->query_factory->newUpdate($connection);
        $this->mapper->modifyUpdate($update, $entity, $initial_data);
        return (bool) $update->exec();
    }

    /**
     * 
     * Deletes an entity from the mapped table using a write connection.
     * 
     * @param object $entity The entity to delete.
     * 
     * @return bool True if the delete succeeded, false if not.  (This is
     * determined by checking the number of rows affected by the query.)
     * 
     */
    public function delete($entity)
    {
        $connection = $this->connections->getWrite();
        $delete = $this->query_factory->newDelete($connection);
        $this->mapper->modifyDelete($delete, $entity);
        return (bool) $delete->exec();
    }

    /**
     * 
     * Returns a new Select object for the mapped table using a read
     * connection.
     * 
     * @param array $cols Select these columns from the table; when empty,
     * selects all mapped columns.
     * 
     * @return Select
     * 
     */
    public function newSelect(array $cols = [])
    {
        $connection = $this->connections->getRead();
        $select = $this->query_factory->newSelect($connection);
        $this->mapper->modifySelect($select, $cols);
        return $select;
    }

    /**
     * 
     * Selects one row from the mapped table for a given column and value(s).
     * 
     * @param string $col The column to use for matching.
     * 
     * @param mixed $val The value(s) to match against; this can be an array
     * of values.
     * 
     * @return array
     * 
     */
    public function fetchOneBy($col, $val)
    {
        $select = $this->newSelectBy($col, $val);
        return $select->fetchOne();
    }

    /**
     * 
     * Selects all rows from the mapped table for a given column and value.
     * 
     * @param string $col The column to use for matching.
     * 
     * @param mixed $val The value(s) to match against; this can be an array
     * of values.
     * 
     * @return array
     * 
     */
    public function fetchAllBy($col, $val)
    {
        $select = $this->newSelectBy($col, $val);
        return $select->fetchAll();
    }

    /**
     * 
     * Creates a Select object to match against a given column and value(s).
     * 
     * @param string $col The column to use for matching.
     * 
     * @param mixed $val The value(s) to match against; this can be an array
     * of values.
     * 
     * @return Select
     * 
     */
    protected function newSelectBy($col, $val)
    {
        $select = $this->newSelect();
        $where = $this->mapper->getTableCol($col);
        if (is_array($val)) {
            $where .= ' IN (?)';
        } else {
            $where .= ' = ?';
        }
        $select->where($where, $val);
        return $select;
    }
}
