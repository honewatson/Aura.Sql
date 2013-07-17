Aura.Sql.Schema
===============

This library provides facilities to read table names and table columns from a
database.


Instantiation
-------------

Instantiate a driver-specific schema object with a matching database
connection:

```php
<?php
use Aura\Sql\Connection\MysqlConnection;
use Aura\Sql\Schema\MysqlSchema;

$connection = new MysqlConnection;
$schema = new MysqlSchema($connection);
?>
```


Retrieving Schema Information
-----------------------------

To get a list of tables in the database, issue `fetchTableList()`:

```php
<?php
$tables = $schema->fetchTableList();
foreach ($tables as $table) {
    echo $table . PHP_EOL;
}
?>
```

To get information about the columns in a table, issue `fetchTableCols()`:

```php
<?php
$cols = $schema->fetchTableCols('table_name');
foreach ($cols as $name => $col) {
    echo "Column $name is of type "
       . $col->type
       . " with a size of "
       . $col->size
       . PHP_EOL;
}
?>
```

Each column description is a `Column` object with the following properties:

- `name`: (string) The column name

- `type`: (string) The column data type.  Data types are as reported by the database.

- `size`: (int) The column size.

- `scale`: (int) The number of decimal places for the column, if any.

- `notnull`: (bool) Is the column marked as `NOT NULL`?

- `default`: (mixed) The default value for the column. Note that sometimes
  this will be `null` if the underlying database is going to set a timestamp
  automatically.

- `autoinc`: (bool) Is the column auto-incremented?

- `primary`: (bool) Is the column part of the primary key?

