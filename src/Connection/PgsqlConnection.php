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
class PgsqlConnection extends AbstractConnection
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

    public function getLastInsertIdName($table = null, $col = null)
    {
        return $this->quoteName("{$table}_{$col}_seq");
    }
}
