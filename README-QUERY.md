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

You can then modify the `Select` object to form your query and call a
`fetch*()` method to get the results.

```php
<?php
// SELECT * FROM foo WHERE bar > :bar ORDER BY baz LIMIT 10 OFFSET 20
$select->cols('*')
       ->from('foo')
       ->where('bar > :bar')
       ->orderBy('baz');
       ->limit(10)
       ->offset(20);

$select->bindValues(['bar' => '88']);
$result = $select->fetchAll();
?>
```

### DISTINCT

Use the `distinct()` method set a DISTINCT clause.

```php
<?php
// SELECT DISTINCT
$select->distinct(true); // false to turn off DISTINCT
?>
```

### Columns

The `cols()` method specifies which columns to select from the `FROM` and
`JOIN` tables. Multiple calls to `cols()` will append to the existing list.

```php
<?php
// SELECT foo, bar AS barbar, baz, dib
$select->cols('foo');
$select->cols('bar AS barbar');
$select->cols(['baz', 'dib']);
?>
```

### FROM

Specify the `FROM` clause using the from() method. Multiple calls to `from()`
will append to the existing list.

```php
<?php
// SELECT ... FROM foo, bar AS barbar, baz, dib
$select->from('foo');
$select->from('bar AS barbar');
$select->from(['baz', 'dib']);
?>
```

To select from a sub-select, use `fromSubSelect()` and pass the query string
with an alias.

```php
<?php
// SELECT ... FROM (SELECT * FROM foo) AS subfoo
$select->fromSubSelect('SELECT * FROM foo', 'subfoo');
?>
```

### JOIN

`JOIN` to another table using the `join()` method.  Pass a join type, the
table (and alias) to join to, and the conditions.

```php
<?php
// SELECT FROM foo AS f INNER JOIN bar AS b ON f.id = b.id
$select->from('foo AS f');
$select->join('inner', 'bar AS b', 'f.id = b.id');

// SELECT FROM foo NATURAL JOIN bar
$select->from('foo');
$select->join('natural', 'bar');
?>
```

To join to a sub-select, use the `joinSubSelect()` method. Pass a join type,
the sub-select query string, an alias for the sub-select, and the conditions.

```php
<?php
// SELECT FROM foo AS f LEFT JOIN (SELECT * FROM bar) AS b ON f.id = b.id
$select->from('foo AS f');
$select->joinSubSelect('left', 'SELECT * FROM bar', 'b', 'f.id = b.id');
?>
```

### WHERE

Set `WHERE` conditions with the `where()` method.  Pass a condition,
optionally with a value to quote into condition immediately. Multiple calls
to `where()` will cause the conditions to be `AND`ed.

```php
<?php
// SELECT ... WHERE foo = bar AND baz = 'dib' AND zim IN (:zim)
$select->where('foo = bar');
$select->where('baz = ?', 'dib');
$select->where('zim IN (:zim)');
?>
```

To add an `OR`, use `orWhere()`.

```php
<?php
// SELECT ... WHERE (foo = bar OR baz = 'dib') AND zim IN (:zim)
// -- note the placement of parentheses to set precedence
$select->where('(foo = bar');
$select->orWhere('baz = ?)', 'dib');
$select->where('zim IN (:zim)');
?>
```

### GROUP BY

To group the results, call `groupBy()`.  Multiple calls to `groupBy()` will
add to the existing groupings.

```php
<?php
// SELECT ... GROUP BY foo, bar, baz, dib
$select->groupBy('foo');
$select->groupBy('bar');
$select->groupBy(['baz', 'dib']);
?>
```

### HAVING

Set `HAVING` conditions with the `having()` method.  Pass a condition,
optionally with a value to quote into condition immediately. Multiple calls
to `having()` will cause the conditions to be `AND`ed.

```php
<?php
// SELECT ... HAVING foo = bar AND baz = 'dib' AND zim IN (:zim)
$select->having('foo = bar');
$select->having('baz = ?', 'dib');
$select->having('zim IN (:zim)');
?>
```

To add an `OR`, use `orHaving()`.

```php
<?php
// SELECT ... HAVING (foo = bar OR baz = 'dib') AND zim IN (:zim)
// -- note the placement of parentheses to set precedence
$select->having('(foo = bar');
$select->orHaving('baz = ?)', 'dib');
$select->having('zim IN (:zim)');
?>
```

### ORDER BY

To order the results, call `orderBy()`.  Multiple calls to `orderBy()` will
add to the existing orderings.

```php
<?php
// SELECT ... ORDER BY foo, bar DESC, baz, dib
$select->orderBy('foo');
$select->orderBy('bar DESC');
$select->orderBy(['baz', 'dib']);
?>
```

### LIMIT ... OFFSET, and Paging

Add a LIMIT and OFFSET with the `limit()` and `offset()` methods.

```php
<?php
// SELECT ... LIMIT 10 OFFSET 20
$select->limit(10);
$select->offset(20);
?>
```

Alternatively, one can get a "page" of results.  Set the number of results
per "page" using `setPaging()`, and then set the page number with `page()`.
(This will reset any existing LIMIT and OFFSET).

```php
<?php
// 10 rows per "page"
$select->setPaging(10);
// select the second "page"
$select->page(2);
?>
```

### UNION

Implement a `UNION` with a second query using the `union()` or `unionAll()`
method; this will convert the existing `Select` object to a string and reset
its clauses for a second query.

```php
<?php
// SELECT id FROM foo
// UNION
// SELECT id FROM bar
// UNION ALL
// SELECT id from baz
// ORDER BY id
$select->cols('id')
       ->from('foo');
       
$select->union()
       ->cols('id')
       ->from('bar');

$select->unionAll()
       ->cols('id')
       ->from('baz')
       ->orderBy('id');
?>
```

### Binding Values
Bind values to the query using the `bindValues()` method. Multiple calls to
`bindValues()` will merge, not reset, the bound values.

```php
<?php
// SELECT * FROM foo WHERE id IN ('1', '2', '3')
$select->cols('*')
       ->from('foo')
       ->where('id IN (:id_list)');

$select->bindValues(['id' => ['1, 2, 3']]);
?>
```

### Fetching Results

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
