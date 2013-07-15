Aura.Sql.Pdo
============

The` Aura.Sql.Pdo` package is extracted from `Aura.Sql`, and provides *only*
the `ExtendedPdo` functionality from that package. This is for developers who
want the lightest possible lower-level convenience of `ExtendedPdo` but not
the driver-specific `Connection` and `Schema` classes, the `Query` classes, or
the `Mapper` functionality.

The `ExtendedPdo` class is an extension of the PHP-native `PDO` class (read
more about PDO [here](http://php.net/PDO).) Among other things, this means
that code already using PDO or typehinted to PDO can use `ExtendedPdo` with no
changes to existing code.

Added functionality in `ExtendedPdo` includes:

- **Lazy connection.** `ExtendedPdo` connects to the database only on method
  calls that require a connection. This means you can create an instance
  and not incur the cost of a connection if you never make a query.

- **Bind values.** You may provide values for binding to the next query using
  `bindValues()`. Mulitple calls to `bindValues()` will merge, not reset, the
  values. The values will be reset after calling `query()`, `exec()`,
  `prepare()`, or any of the `fetch*()` methods.  In addition, binding values
  that do not have any corresponding placeholders will not cause an error.

- **Array quoting.** The `quote()` method will accept an array as input, and
  return a string of comma-separated quoted values. In addition, named
  placeholders in prepared statements that are bound to array values will
  be replaced with comma-separated quoted values. This means you can bind
  an array of values to a placeholder used with an `IN (...)` condition.

- **Fetch methods.** The class provides several `fetch*()` methods to reduce
  boilerplate code elsewhere. For example, you can call `fetchAll()` directly
  on the instance instead of having to prepare a statement, bind values,
  execute, and then fetch from the prepared statement. All of the `fetch*()`
  methods take an array of values to bind to to the query statement.

- **Profiler**.  An optional query profiler is provided.

- **Exceptions by default.** `ExendedPdo` starts in the `ERRMODE_EXCEPTION`
  mode for error reporting instead of `ERRMODE_SILENT`.


Autoloading
-----------

`ExtendedPdo` comes with an autoloader; to add it to the SPL autoload stack,
require or include the `autoload.php` file.

```php
<?php
require '/path/to/Aura.Sql.Pdo/autoload.php';
?>
```

You can then instantiate `ExtendedPdo`.


Instantiation
-------------

Instantiation is the same as with PDO: pass a data source name, username,
password, and driver options.  There is one additional parameter that
allows you to pass PDO attributes to be set after the connection is made.

```php
<?php
use Aura\Sql\Pdo\ExtendedPdo;

$pdo = new ExtendedPdo(
    'mysql:host=localhost;dbname=test',
    'username',
    'password',
    [], // driver options as key-value pairs
    []  // attributes as key-value pairs
);
?>
```


Lazy Connection
---------------

Whereas PDO connects on instantiation, `ExtendedPdo` does not connect
immediately. Instead, it connects only when you call a method that actually
needs the connection to the database; e.g., on `query()`.

If you want to force a connection, call the `connect()` method.

```php
<?php
use Aura\Sql\Pdo\ExtendedPdo;

// does not connect to the database
$pdo = new ExtendedPdo(
    'mysql:host=localhost;dbname=test',
    'username',
    'password'
);

// automatically connects
$pdo->exec('SELECT * FROM test');

// explicitly forces a connection
$pdo->connect();
?>
```

Bind Values
-----------

Instead of having to bind values to a prepared PDOStatement, you can call
`bindValues()` directly on the `ExtendedPdo` instance, and those values will
be bound to named placeholders in the next query.

```php
<?php
// normal PDO way
$pdo = new PDO(...);
$sth = $pdo->prepare('SELECT * FROM test WHERE foo = :foo AND bar = :bar');
$sth->bindValue('foo', 'foo_value');
$sth->bindValue('bar', 'bar_value');
$stm = $sth->execute();

// ExtendedPdo
$pdo = new ExtendedPdo(...);
$pdo->bindValues([ 'foo' => 'foo_value', 'bar' => 'bar_value']);
$sth = $pdo->query('SELECT * FROM test WHERE foo = :foo AND bar = :bar');
?>
```

Array Quoting
-------------

The normal `PDO::quote()` method will not quote arrays. This makes it
difficult to bind an array to something like an `IN (...)` condition in SQL.
However, `ExtendedPdo` recognizes arrays and converts them into
comma-separated quoted strings.

```php
<?php
// the array to be quoted
$array = ['foo', 'bar', 'baz'];

// the normal PDO way:
// "Warning:  PDO::quote() expects parameter 1 to be string, array given"
$pdo = new Pdo(...);
$cond = 'IN (' . $pdo->quote($array) . ')';

// the ExtendedPdo way:
// "IN ('foo', 'bar', 'baz')"
$pdo = new ExtendedPdo(...);
$cond = 'IN (' . $pdo->quote($array) . ')'; 
?>
```

Whereas the normal `PDO::prepare()` does not deal with bound array values,
`ExtendedPdo` modifies the query string to replace the named placeholder with
the quoted array.  Note that this is *not* the same thing as binding proper;
the query string itself is modified before passing to the database for value
binding.

```php
<?php
// the array to be quoted
$array = ['foo', 'bar', 'baz'];

// the statement to prepare
$stm = 'SELECT * FROM test WHERE foo IN (:foo) AND bar = :bar'

// the normal PDO way does not work (PHP Notice:  Array to string conversion)
$pdo = new Pdo(...);
$sth = $pdo->prepare($stm);
$sth->bindValue('foo', $array);

// the ExtendedPdo way quotes the array and replaces the array placeholder
// directly in the query string
$pdo = new ExtendedPdo(...);
$pdo->bindValues(
    'foo' => ['foo', 'bar', 'baz'],
    'bar' => 'qux',
);
$sth = $pdo->prepare($stm);
echo $sth->queryString;
// the query string has been modified by ExtendedPdo to become
// "SELECT * FROM test WHERE foo IN ('foo', 'bar', 'baz') AND bar = :bar"
?>
```

Finally, note that array quoting works only on the `ExtendedPdo` instance, not
on returned PDOStatement instances.


Fetch Methods
-------------

`ExtendedPdo` comes with `fetch*()` methods to reduce boilerplate code. Instead
of issuing prepare(), a series of bindValue() calls, execute(), and then fetch*()
on a PDOStatement, you can bind values and fetch results in one call.

```php
<?php
$stm  = 'SELECT * FROM test WHERE foo = :foo AND bar = :bar';
$bind = array('foo' => 'bar', 'baz' => 'dib');

// the normal PDO way to "fetch all" where the result is a sequential array
// of rows, and the row arrays are keyed on the column names
$pdo = new PDO(...);
$pdo->prepare($stm);
$stm->bindValue('foo', $bind['foo']);
$stm->bindValue('bar', $bind['bar']);
$sth = $stm->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

// the ExtendedPdo way to "fetch all"
$pdo = new ExtendedPdo(...);
$result = $pdo->fetchAll($stm, $bind);

// fetchAssoc() returns an associative array of all rows where the key is the
// first column, and the row arrays are keyed on the column names
$result = $pdo->fetchAssoc($stm, $bind);

// fetchCol() returns a sequential array of all values in the first column
$result = $pdo->fetchCol($stm, $bind);

// fetchOne() returns the first row as an associative array where the keys
// are the column names
$result = $pdo->fetchOne($stm, $bind);

// fetchPairs() returns an associative array where each key is the first
// column and each value is the second column
$result = $pdo->fetchPairs($stm, $bind);

// fetchValue() returns the value of the first row in the first column
$result = $pdo->fetchValue($stm, $bind);
?>
```

Profiler
--------

When debugging, it is often useful to see what queries have been executed,
where they were issued from in the codebase, and how long they took to
complete. `ExtendedPdo` comes with an optional profiler that you can use to
discover that information.

```php
<?php
use Aura\Sql\Pdo\ExtendedPdo;
use Aura\Sql\Pdo\Profiler;

$pdo = new ExtendedPdo(...);
$pdo->setProfiler(new Profiler);

// ...
// query(), fetch(), beginTransaction()/commit()/rollback() etc.
// ...

// now retrieve the profile information:
$profiles = $pdo->getProfiler()->getProfiles();
?>
```

Each profile entry will have these keys:

- `method`: The method that was called on `ExtendedPdo` that created the
  profile entry.

- `duration`: How long the query took to complete, in seconds.

- `statement`: The query string that was issued, if any.  (Methods like `connect()`
  and `rollBack()` do not send query strings.)

- `bind_values`: Any values that were bound to the query.

- `trace`: An exception stack trace indicating where the query was issued from
  in the codebase.

Setting the `Profiler` into the `ExtendedPdo` instance is optional. Once it it
set, you can activate and deactivate it as you wish using the
`Profiler::setActive()` method. When not active, query profiles will not be
retained.

```php
<?php
$pdo = new ExtendedPdo(...);
$pdo->setProfiler(new Profiler);

// deactivate, issue a query, and reactivate;
// the query will not show up in the profiles
$pdo->getProfiler()->setActive(false);
$pdo->fetchAll('SELECT * FROM foo');
$pdo->getProfiler()->setActive(true);
?>
```
