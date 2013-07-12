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
 * Microsoft SQL Server connection adapter.
 * 
 * @package Aura.Sql
 * 
 */
class Sqlsrv extends AbstractConnection
{
    /**
     * 
     * The PDO DSN for the connection. This can be an array of key-value pairs
     * or a string (minus the PDO type prefix).
     * 
     * @var string|array
     * 
     */
    protected $dsn = [
        'Server' => null,
        'Database' => null,
    ];

    /**
     * 
     * The PDO type prefix.
     * 
     * @var string
     * 
     */
    protected $dsn_prefix = 'sqlsrv';

    /**
     * 
     * The prefix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_prefix = '[';

    /**
     * 
     * The suffix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_suffix = ']';

    /**
     * 
     * Modifies an SQL string **in place** to add a `TOP` or 
     * `OFFSET ... FETCH NEXT` clause.
     * 
     * @param string $text The SQL string.
     * 
     * @param int $count The number of rows to return.
     * 
     * @param int $offset Skip this many rows first.
     * 
     * @return void
     * 
     */
    public function limit(&$text, $count, $offset = 0)
    {
        $count  = (int) $count;
        $offset = (int) $offset;

        if ($count && ! $offset) {
            // count, but no offset, so we can use TOP
            $text = preg_replace('/^(SELECT( DISTINCT)?)/', "$1 TOP $count", $text);
        } elseif ($count && $offset) {
            // count and offset, use FETCH NEXT
            $text .= "OFFSET $offset ROWS" . PHP_EOL
                   . "FETCH NEXT $count ROWS ONLY" . PHP_EOL;
        }
    }
}
