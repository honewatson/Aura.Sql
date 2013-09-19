Aura.Sql.Connection
===================

This library is part of [`Aura.Sql`](README.md).  It is an extra
extension to `Pdo` to provide driver-specific functionality, such as
quoting of identifier names, getting the last insert ID, and adding `LIMIT`
clauses to `SELECT` statements.

In general, you are not likely to need the `Connection` object itself very
often; instead, we think you will more frequently use a `ConnectionLocator`
combined with a `QueryFactory` to build and execute SELECT, INSERT, UPDATE,
and DELETE statements in an object-oriented fashion.


Instantiation
-------------

Instantiate a `Connection` the same way you would a `PDO` or `Pdo`
object. Be sure to pick the right driver type for your connection DSN.

```php
<?php
use Aura\Sql;

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

As the `Connection` classes extend `Pdo`, which itself extends `PDO`,
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

Connection Locator
------------------

The `ConnectionLocator` is a registry/factory for connections; it allows you
to specify a default connection, one or more named read connections, and one
or more names write connections. Then you can pick which connection you want
to use for your own purposes.

- `ConnectionLocator::getDefault()` returns the default connection.

- `ConnectionLocator::getRead($name = null)` returns a named read connection;
  if no name is specified, returns a random read connection; if there are no
  read connections, returns the default connection.

- `ConnectionLocator::getWrite($name = null)` returns a named write
  connection; if no name is specified, returns a random write connection; if
  there are no write connections, returns the default connection.

Each connection is wrapped in a closure that contains the instantiation logic.

The following example programmatically sets up a single default connection:

```php
<?php
$connections = new ConnectionLocator;
$connections->setDefault(function () {
    return new SqliteConnection(
        'dsn' => 'sqlite::memory:',
    );
});
?>
```

The next example programmatically sets up one master and two slaves.

```php
<?php
$connections = new ConnectionLocator;

$connections->setWrite('master', function () {
    return new MysqlConnection(
        'dsn' => 'mysql:host=write.db.example.com;dbname=example',
        'username' => 'username',
        'password' => 'password'
    );
});

$connections->setRead('slave1', function () {
    return new MysqlConnection(
        'dsn' => 'mysql:host=read1.db.example.com;dbname=example',
        'username' => 'username',
        'password' => 'password'
    );
});

$connections->setRead('slave2', function () {
    return new MysqlConnection(
        'dsn' => 'mysql:host=read2.db.example.com;dbname=example',
        'username' => 'username',
        'password' => 'password'
    );
});
?>
```

Finally, you can pass the default, read, and write connection closures as
constructor parameters.

```php
<?php
$default = null;

$read = [
    'slave1' => function () {
        return new MysqlConnection(
            'dsn' => 'mysql:host=read1.db.example.com;dbname=example',
            'username' => 'username',
            'password' => 'password'
        );
    },
    'slave2' => function () {
        return new MysqlConnection(
            'dsn' => 'mysql:host=read2.db.example.com;dbname=example',
            'username' => 'username',
            'password' => 'password'
        );
    },
];

$write = [
    'master' => function () {
        return new MysqlConnection(
            'dsn' => 'mysql:host=write.db.example.com;dbname=example',
            'username' => 'username',
            'password' => 'password'
        );
    },
];

$connections = new ConnectionLocator($default, $read, $write);
?>
```
