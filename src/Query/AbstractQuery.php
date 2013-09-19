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

use Aura\Sql\Pdo\PdoInterface;

/**
 * 
 * Abstract query object for Select, Insert, Update, and Delete.
 * 
 * @package Aura.Sql
 * 
 */
abstract class AbstractQuery
{
    /**
     * 
     * An SQL connection.
     * 
     * @var PdoInterface
     * 
     */
    protected $pdo;

    /**
     * 
     * Values to bind to the query.
     * 
     * @var array
     * 
     */
    protected $bind_values = [];

    /**
     * 
     * Constructor.
     * 
     * @param PdoInterface pdo An SQL connection.
     * 
     * @return void
     * 
     */
    public function __construct(PdoInterface pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 
     * Converts this query object to a string.
     * 
     * @return string
     * 
     */
    abstract public function __toString();

    /**
     * 
     * Gets the database connection for this query object.
     * 
     * @return PdoInterface
     * 
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * 
     * Returns an array as an indented comma-separated values string.
     * 
     * @param array $list The values to convert.
     * 
     * @return string
     * 
     */
    protected function indentCsv(array $list)
    {
        return PHP_EOL
             . '    ' . implode(',' . PHP_EOL . '    ', $list)
             . PHP_EOL;
    }

    /**
     * 
     * Returns an array as an indented string.
     * 
     * @param array $list The values to convert.
     * 
     * @return string
     * 
     */
    protected function indent($list)
    {
        return PHP_EOL
             . '    ' . implode(PHP_EOL . '    ', $list)
             . PHP_EOL;
    }

    /**
     * 
     * Adds values to bind into the query; merges with existing values.
     * 
     * @param array $bind Values to bind to the query.
     * 
     * @return void
     * 
     */
    public function bindValues(array $bind_values)
    {
        $this->bind_values = array_merge($this->bind_values, $bind_values);
    }

    /**
     * 
     * Gets the values to bind into the query.
     * 
     * @return array
     * 
     */
    public function getBindValues()
    {
        return $this->bind_values;
    }
    
    /**
     * 
     * Executes the query and returns the number of rows affected.
     * 
     */
    public function exec()
    {
        $this->pdo->bindValues($this->getBindValues());
        return $this->pdo->exec($this->__toString());
    }
    
    /**
     * 
     * Executes the query and returns a PDOStatement.
     * 
     */
    public function query()
    {
        $this->pdo->bindValues($this->getBindValues());
        return $this->pdo->query($this->__toString());
    }
}
