Aura.Sql.Schema
===============

TBD.

* * *

Retrieving Table Information
----------------------------

To get a list of tables in the database, issue `fetchTableList()`:

```php
<?php
// get the list of tables
$list = $connection->fetchTableList();

// show them
foreach ($list as $table) {
    echo $table . PHP_EOL;
}
?>
```

To get information about the columns in a table, issue `fetchTableCols()`:

```php
<?php
// the table to get cols for
$table = 'foo';

// get the cols
$cols = $connection->fetchTableCols($table);

// show them
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

- `default`: (mixed) The default value for the column. Note that sometimes this will be `null` if the underlying database is going to set a timestamp automatically.

- `autoinc`: (bool) Is the column auto-incremented?

- `primary`: (bool) Is the column part of the primary key?

