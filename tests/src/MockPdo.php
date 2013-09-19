<?php
namespace Aura\Sql;

class MockPdo extends Pdo
{
    protected $quote_name_prefix = '"';
    
    protected $quote_name_suffix = '"';
    
    public function getDsn()
    {
        return $this->dsn;
    }
    
    public function quote($val, $type = Pdo::PARAM_STR)
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
