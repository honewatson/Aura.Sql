Aura.Sql.Query
==============

This library provides an object-oriented way to build and execute SQL queries,
generally via a `QueryFactory`.

Instantiation
-------------

Instantiate a `QueryFactory` like so (you will need a database connection
later):

```php
<?php
use Aura\Sql\Query\QueryFactory;
use Aura\Sql\Connection\MysqlConnection;

$query_factory = new QueryFactory;
?>
```

Select
------

To get a new `Select` object, call `QueryFactory::newSelect()` and pass it
a `Connection` object.

```php
<?php
$connection = new MysqlConnection(...);
$select = $query_factory->newSelect($connection);
?>
```

You can then modify the `Select` object to form your query.

```php
<?php
// SELECT * FROM foo WHERE bar > :bar ORDER BY baz LIMIT 10 OFFSET 20
$select->cols(['*'])
       ->from('foo')
       ->where('bar > :bar')
       ->orderBy('baz');
       ->limit(10)
       ->offset(20);
?>
```

The `Select` object has the following methods and more; please read the source
code for more information.

- `distinct($flag)`: Set to `true` for a `SELECT DISTINCT`.

- `cols((array) $cols)`: Select these columns.

- `from((array) $tables)`: Select from these tables.

- `join($type, $table, $on)`: Join these tables on specified conditions.

- `where($cond, $value)`: `WHERE` these conditions are met (using `AND`).

- `orWhere($cond, $value)`: `WHERE` these conditions are met (using `OR`).

- `groupBy((array) $cols)`: `GROUP BY` these columns.

- `having($cond, $value)`: `HAVING` these conditions met (using `AND`).

- `orHaving($cond, $value)`: `HAVING` these conditions met (using `OR`).

- `orderBy((array) $cols)`: `ORDER BY` these columns.

- `limit($count)`: `LIMIT` to this many rows.

- `offset($offset)`: `OFFSET` by this many rows.

- `union()`: `UNION` with a followup `SELECT`.

- `unionAll()`: `UNION ALL` with a followup `SELECT`.

Once you build the query, you can then bind values and fetch results:

```php
<?php
$select->bindValues(['bar' => '88']);
$result = $select->fetchAll();
?>
```

The `Select` object has the same `fetch*()` methods as the `ExtendedPdo`
object:

- `fetchAll()` returns a sequential array of rows, and the row arrays are
  keyed on the column names

- `fetchAssoc()` returns an associative array of all rows where the key is the
  first column, and the row arrays are keyed on the column names

- `fetchCol()` returns a sequential array of all values in the first column

- `fetchOne()` returns the first row as an associative array where the keys
  are the column names

- `fetchPairs()` returns an associative array where each key is the first
  column and each value is the second column

- `fetchValue()` returns the value of the first row in the first column


Insert
------

To get a new `Insert` object, call `QueryFactory::newInsert()` and pass it
a `Connection` object.

```php
<?php
$connection = new MysqlConnection(...);
$insert = $query_factory->newInsert($connection);
?>
```

You can then modify the `Insert` object to form your query.

```php
<?php
// INSERT INTO foo (bar, baz, date) VALUES (:bar, :baz, NOW());
$insert->into('foo')
       ->cols(['bar', 'baz'])
       ->set('date', 'NOW()');
?>
```

Finally, you can bind values to the query, execute it, and get back the last
insert ID (note that you should pass the column name for portability
purposes).

```php
<?php
// bind these values to the insert
$insert->bindValues([
    'bar' => 'dib',
    'baz' => 'zim',
]);

// execute the insert
$success = $insert->exec();

// did it work?
if ($success) {
    // show the last insert id
    echo $insert->lastInsertId('id');
}
?>
```

Update
------

To get a new `Update` object, call `QueryFactory::newUpdate()` and pass it
a `Connection` object.

```php
<?php
$connection = new MysqlConnection(...);
$update = $query_factory->newUpdate($connection);
?>
```

You can then modify the `Update` object to form your query.

```php
<?php
// UPDATE foo SET bar = :bar, baz = :baz, date = NOW() WHERE zim = :zim OR gir = :gir
$update->table('foo')
       ->cols(['bar', 'baz'])
       ->set('date', 'NOW()')
       ->where('zim = :zim')
       ->orWhere('gir = :gir');
?>
```

Finally, you can bind values to the query and execute it, getting back the
number of affected rows.

```php
<?php
$update->bindValues = ([
    'bar' => 'barbar',
    'baz' => 99,
    'zim' => 'dib',
    'gir' => 'doom',
]);

$row_count = $update->exec();
?>
```

Delete
------

To get a new `Delete` object, call `QueryFactory::newDelete()` and pass it
a `Connection` object.

```php
<?php
$connection = new MysqlConnection(...);
$delete = $query_factory->newDelete($connection);
?>
```

You can then modify the `Delete` object to form your query.

```php
<?php
// DELETE FROM foo WHERE zim = :zim OR gir = :gir
$delete->from('foo')
       ->where('zim = :zim')
       ->orWhere('gir = :gir');
?>
```

Finally, you can bind values to the query and execute it, getting back the
number of affected rows.

```php
<?php
$delete->bindValues([
    'zim' => 'dib',
    'gir' => 'doom',
]);

$row_count = $delete->exec();
?>
```
