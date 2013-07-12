<?php
namespace Aura\Sql\Connection;

class Mock extends AbstractConnection
{
    protected $quote_name_prefix = '"';
    
    protected $quote_name_suffix = '"';
    
    public function getDsn()
    {
        return $this->dsn;
    }
    
    public function quote($val, $type = ExtendedPdo::PARAM_STR)
    {
        return "'" . strtr(
            $val,
            [
                '\\' => '\\\\',
                "'" => "\'"
            ]
        ) . "'";
    }
}
