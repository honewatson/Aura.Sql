<?php
namespace Aura\Sql\Connection;

class MysqlTest extends AbstractConnectionTest
{
    protected $extension = 'pdo_mysql';
    
    protected $connection_type = 'mysql';    
    
    protected $expect_quote_scalar = "'\\\"foo\\\" bar \\'baz\\''";
    
    protected $expect_quote_array = "'\\\"foo\\\"', 'bar', '\'baz\''";
    
    protected $expect_quote_into = "foo = '\'bar\''";
    
    protected $expect_quote_values_in = "foo = '\'bar\'' AND zim = '\'baz\''";
    
    protected $expect_quote_name_table_as_alias = '`table` AS `alias`';
    
    protected $expect_quote_name_table_col_as_alias = '`table`.`col` AS `alias`';
    
    protected $expect_quote_name_table_alias = '`table` `alias`';
    
    protected $expect_quote_name_table_col_alias = '`table`.`col` `alias`';
    
    protected $expect_quote_name_plain = '`table`';
    
    protected $expect_quote_names_in = "*, *.*, `foo`.`bar`, CONCAT('foo.bar', \"baz.dib\") AS `zim`";
}
