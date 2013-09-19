<?php
namespace Aura\Sql;

class PdoFactory
{
    protected $attributes = array(
        'mysql' => array(
            Pdo::ATTR_QUOTE_NAME_PREFIX => '`',
            Pdo::ATTR_QUOTE_NAME_SUFFIX => '`',
        ),
        'sqlsrv' => array(
            Pdo::ATTR_QUOTE_NAME_PREFIX => '[',
            Pdo::ATTR_QUOTE_NAME_SUFFIX => ']',
        ),
        
    );
    
    public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
    }
    
    public function newInstance(
        $dsn,
        $username = null,
        $password = null,
        array $options = array(),
        array $attributes = array()
    ) {
        $pos = strpos($dsn, ':');
        $type = substr($dsn, 0, $pos);
        if (isset($this->attributes[$type])) {
            $attributes = array_merge($this->attributes[$type], $attributes);
        }
        return new Pdo($dsn, $username, $password, $options, $attributes);
    }
}
