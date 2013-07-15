Aura.Sql.Connection
===================

This libarary is part of [`Aura.Sql`](README.md).  It is an extra
extension to `ExtendedPdo` to provide driver-specific functionality, such as
quoting of identifier names, getting the last insert ID, and adding `LIMIT`
clauses to `SELECT` statements.


Instantiation
-------------

Instantiate a `Connection` the same way you would a `PDO` or `ExtendedPdo`
object. Be sure to pick the right driver type for your connection DSN.

```php
    <?php
    use Aura\Sql\Connection;

    // MySQL
    $conn = new MysqlConnection(
        'mysql:host=localhost;dbname=test',
        'username',
        'password'
    );

    // PostgreSQL
    $conn = new PgsqlConnection(
        'pgsql:host=localhost;dbname=test',
        'username',
        'password'
    );

    // Sqlite
    $conn = new SqliteConnection(
        'sqlite::memory:'
    );
    ?>
```

As the `Connection` classes extend `ExtendedPdo`, which itself extends `PDO`,
you should be able to use a `Connection` class in place of the other two
classes with no trouble.


Last Insert ID
--------------

The `Connection` classes provide an abstracted way to get the last insert ID
across the different drivers. You should provide the table and column name you
inserted on, to get the last insert ID. Typically, most databases do not
actually need these, but some (such as PostgreSQL) require that information to
determine the right sequence name. When using a `Connection` class, get into
the habit of passing the table and column name.

```php
<?php
$conn->bindValues('foo' => 'bar');
$conn->exec('INSERT INTO test (id, foo) VALUES (null, :foo)');
$id = $conn->lastInsertId('test', 'id');
?>
```

LIMIT Count and Offset
----------------------

You can append a driver-specific `LIMIT` count and offset to a query statement
using the `limit()` method.

```php
<?php
$stm = 'SELECT * FROM test'
$conn->limit($stm, 10, 20);
$result = $conn->fetchAll($stm); // 'SELECT * FROM test LIMIT 10 OFFSET 20'
?>
```

Quoting Identifier Names
------------------------

It's often useful to quote not only values, but also database identifiers such
as table and column names.  The `quoteName()` and `quoteNamesIn()` methods do
so nicely.

```php
<?php
// quote names one at a time
$conn = new MysqlConnection(...);
$stm = 'SELECT ' . $conn->quoteName('foo')
     . ' FROM ' . $conn->quoteName('test');
$conn->fetchAll($stm); // 'SELECT `foo` FROM `test`'

// quote all dotted and aliased names in a string
$stm = 'SELECT t.foo, t.bar, t.baz FROM test AS t';
$stm = $conn->quoteNamesIn($stm);
$conn->fetchAll($stm); // 'SELECT `t`.`foo`, `t`.`bar`, `t`.`baz` FROM `test` AS `t`
?>
```
