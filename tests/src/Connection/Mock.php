<?php
namespace Aura\Sql\Connection;

class Mock extends AbstractConnection
{
    protected $dsn_prefix = 'mock';
    
    protected $quote_name_prefix = '"';
    
    protected $quote_name_suffix = '"';
    
    public function quote($val)
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

// namespace Aura\Sql\Connection;
// use Aura\Sql\ProfilerInterface;
// use Aura\Sql\ColumnFactory;
// use Aura\Sql\Query\Factory as QueryFactory;
// 
// class Mock extends AbstractConnection
// {
//     protected $dsn_string = 'mock';
//     
//     protected $params = [];
//     
//     public function __construct(
//         ProfilerInterface $profiler,
//         QueryFactory $query_factory,
//         $dsn,
//         $username = null,
//         $password = null,
//         array $options = []
//     ) {
//         parent::__construct(
//             $profiler,
//             $query_factory,
//             $dsn,
//             $username,
//             $password,
//             $options
//         );
//         
//         $this->params = [
//             'dsn'      => $dsn,
//             'username' => $username,
//             'password' => $password,
//             'options'  => $options,
//         ];
//     }
//     
//     public function getParams()
//     {
//         return $this->params;
//     }
//     
//     public function getDsnHost()
//     {
//         return $this->params['dsn']['host'];
//     }
//     
//     public function fetchTableList($schema = null)
//     {
//         return [];
//     }
//     
//     public function fetchTableCols($spec)
//     {
//         return [];
//     }
// }
