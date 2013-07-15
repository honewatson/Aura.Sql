Aura.Sql.Query
==============

This library provides 
* * *

Query Objects
=============

Aura SQL provides four types of query objects so you can write your SQL
queries in an object-oriented way.

Select
------

To get a new `Select` object, invoke the `newSelect()` method on an connection.
You can then modify the `Select` object and pass it to the `query()` or
`fetch*()` method.

```php
<?php
// create a new Select object
$select = $connection->newSelect();

// SELECT * FROM foo WHERE bar > :bar ORDER BY baz
$select->cols(['*'])
       ->from('foo')
       ->where('bar > :bar')
       ->orderBy('baz');

$bind = ['bar' => '88'];

$list = $connection->fetchAll($select, $bind);
?>
```

The `Select` object has these methods and more; please read the source code
for more information.

- `distinct()`: Set to `true` for a `SELECT DISTINCT`.

- `cols()`: Select these columns.

- `from()`: Select from these tables.

- `join()`: Join these tables on specified conditions.

- `where()`: `WHERE` these conditions are met (using `AND`).

- `orWhere()`: `WHERE` these conditions are met (using `OR`).

- `groupBy()`: `GROUP BY` these columns.

- `having()`: `HAVING` these conditions met (using `AND`).

- `orHaving()`: `HAVING` these conditions met (using `OR`).

- `orderBy()`: `ORDER BY` these columns.

- `limit()`: `LIMIT` to this many rows.

- `offset()`: `OFFSET` by this many rows.

- `union()`: `UNION` with a followup `SELECT`.

- `unionAll()`: `UNION ALL` with a followup `SELECT`.

Insert
------

To get a new `Insert` object, invoke the `newInsert()` method on an connection.
You can then modify the `Insert` object and pass it to the `query()` method.

```php
<?php
// create a new Insert object
$insert = $connection->newInsert();

// INSERT INTO foo (bar, baz, date) VALUES (:bar, :baz, NOW());
$insert->into('foo')
       ->cols(['bar', 'baz'])
       ->set('date', 'NOW()');

$bind = [
    'bar' => null,
    'baz' => 'zim',
];

$stmt = $connection->query($insert, $bind);
?>
```

Update
------

To get a new `Update` object, invoke the `newUpdate()` method on an connection.
You can then modify the `Update` object and pass it to the `query()` method.

```php
<?php
// create a new Update object
$update = $connection->newUpdate();

// UPDATE foo SET bar = :bar, baz = :baz, date = NOW() WHERE zim = :zim OR gir = :gir
$update->table('foo')
       ->cols(['bar', 'baz'])
       ->set('date', 'NOW()')
       ->where('zim = :zim')
       ->orWhere('gir = :gir');

$bind = [
    'bar' => 'barbar',
    'baz' => 99,
    'zim' => 'dib',
    'gir' => 'doom',
];

$stmt = $connection->query($update, $bind);
?>
```

Delete
------

To get a new `Delete` object, invoke the `newDelete()` method on an connection.
You can then modify the `Delete` object and pass it to the `query()` method.

```php
<?php
// create a new Delete object
$delete = $connection->newDelete();

// DELETE FROM WHERE zim = :zim OR gir = :gir
$delete->from('foo')
       ->where('zim = :zim')
       ->orWhere('gir = :gir');

$bind = [
    'zim' => 'dib',
    'gir' => 'doom',
];

$stmt = $connection->query($delete, $bind);
?>
```
