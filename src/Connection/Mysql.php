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
 * MySQL connection adapter.
 * 
 * @package Aura.Sql
 * 
 */
class Mysql extends AbstractConnection
{
    /**
     * 
     * The prefix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_prefix = '`';

    /**
     * 
     * The suffix to use when quoting identifier names.
     * 
     * @var string
     * 
     */
    protected $quote_name_suffix = '`';
}
