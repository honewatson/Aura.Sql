Aura.Sql.Connection
===================

TBD.

* * *

Instantiation
-------------

The easiest way to get started is to use the `scripts/instance.php` script to
get a `ConnectionFactory` and create your connection through it:

```php
<?php
$connection_factory = include '/path/to/Aura.Sql/scripts/instance.php';
$connection = $connection_factory->newInstance(
    
    // adapter name
    'mysql',
    
    // DSN elements for PDO; this can also be
    // an array of key-value pairs
    'host=localhost;dbname=database_name',
    
    // username for the connection
    'username',
    
    // password for the connection
    'password'
);
?>
```

Alternatively, you can add `'/path/to/Aura.Sql/src'` to your autoloader and
build an connection factory manually:
    
```php
<?php
use Aura\Sql\ConnectionFactory;
$connection_factory = new ConnectionFactory;
$connection = $connection_factory->newInstance(...);
?>
```
    
Aura SQL comes with four connection adapters: `'mysql'` for MySQL, `'pgsql'`
for PostgreSQL, `'sqlite'` for SQLite3, and `'sqlsrv'` for Microsoft SQL
Server.

Connecting
----------

The connection will lazy-connect to the database the first time you issue a
query of any sort. This means you can create the connection object, and if you
never issue a query, it will never connect to the database.

You can connect manually by issuing `connect()`:

```php
<?php
$connection->connect();
?>
```

Preventing SQL Injection
------------------------

Usually you will need to incorporate user-provided data into the query. This
means you should quote all values interpolated into the query text as a
security measure to [prevent SQL injection](http://bobby-tables.com/).

Although Aura SQL provides quoting methods, you should instead use value
binding into prepared statements. To do so, put named placeholders in the
query text, then pass an array of values to bind to the placeholders:

```php
<?php
// the text of the query
$text = 'SELECT * FROM foo WHERE id = :id';

// values to bind to query placeholders
$bind = [
    'id' => 1,
];

// returns one row; the data has been parameterized
// into a prepared statement for you
$result = $connection->fetchOne($text, $bind);
?>
```

Aura SQL recognizes array values and quotes them as comma-separated lists:

```php
<?php
// the text of the query
$text = 'SELECT * FROM foo WHERE id = :id AND bar IN(:bar_list)';

// values to bind to query placeholders
$bind = [
    'id' => 1,
    'bar_list' => ['a', 'b', 'c'],
];

// returns all rows; the query ends up being
// "SELECT * FROM foo WHERE id = 1 AND bar IN('a', 'b', 'c')"
$result = $connection->fetchOne($text, $bind);
?>
```

Transactions
------------

Aura SQL connections always start in autocommit mode (the same as PDO). However,
you can turn off autocommit mode and start a transaction with
`beginTransaction()`, then either `commit()` or `rollBack()` the transaction.
Commits and rollbacks cause the connection to go back into autocommit mode.

```php
<?php
// turn off autocommit and start a transaction
$connection->beginTransaction();

try {
    // ... perform some queries ...
    // now commit to the database:
    $connection->commit();
} catch (Exception $e) {
    // there was an error, roll back the queries
    $connection->rollBack();
}

// at this point we are back in autocommit mode
```

    
Manual Queries
--------------

You can, of course, build and issue your own queries by hand. Use the
`query()` method to do so:

```php
<?php
$text = "SELECT * FROM foo WHERE id = :id";
$bind = ['id' => 1];
$stmt = $connection->query($text, $bind)
```

The returned `$stmt` is a [PDOStatement](http://php.net/PDOStatement) that you
may manipulate as you wish.

Profiling
---------

You can use profiling to see how well your queries are performing.

```php
<?php
// turn on the profiler
$connection->getProfiler()->setActive(true);

// issue a query
$result = $connection->fetchAll('SELECT * FROM foo');

// now get the profiler information
foreach ($connection->getProfiler()->getProfiles() as $i => $profile) {
    echo 'Query #' . ($i + 1)
       . ' took ' . $profile->time . ' seconds.'
       . PHP_EOL;
}
```
    
Each profile object has these properties:

- `text`: (string) The text of the query.

- `time`: (float) The time, in seconds, for the query to finish.

- `data`: (array) Any data bound to the query.

- `trace`: (array) A [debug_backtrace](http://php.net/debug_backtrace) so
  you can tell where the query came from.


