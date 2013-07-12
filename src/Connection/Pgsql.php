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
 * PostgreSQL connection adapter.
 * 
 * @package Aura.Sql
 * 
 */
class Pgsql extends AbstractConnection
{
    /**
     * 
     * The prefix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_prefix = '"';

    /**
     * 
     * The suffix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_suffix = '"';

    /**
     * 
     * Returns the last ID inserted on the connection for a given table
     * and column sequence.
     * 
     * PostgreSQL uses a sequence named for the table and column to track
     * auto-incremented IDs; you need to pass the table and column name to
     * tell PostgreSQL which sequence to check.
     * 
     * @param string $table The table to check the last inserted ID on.
     * 
     * @param string $col The column to check the last inserted ID on.
     * 
     * @return mixed
     * 
     */
    public function getLastInsertIdName($table = null, $col = null)
    {
        return $this->quoteName("{$table}_{$col}_seq");
    }
}
