Aura.Sql.Pdo
============

The Aura.Sql.Pdo package is extracted from Aura.Sql, and provides *only* the
`ExtendedPdo` functionality. This is for developers who want the lightest
possible lower-level convenience of `ExtendedPdo` but not the driver-specific
`Connection` and `Schema` classes, or the `Query` and `Mapper` functionality.

Added functionality includes:

- **Lazy connection.** `ExtendedPdo` connects to the database only on method
  calls that require a connection. This means you can create an instance
  and not incur the cost of a connection if you never make a query.

- **Array quoting.** The `quote()` method will accept an array as input, and
  return a string of comma-separated quoted values. In addition, named
  placeholders in prepared statements that are bound to array values will
  be replaced with comma-separated quoted values. This means you can bind
  an array of values to a placeholder used with an `IN (...)` condition.

- **Bind values.** You may provide values for binding to the next query using
  `bindValues()`. Mulitple calls to `bindValues()` will merge, not reset, the
  values. The values will be reset after calling `query()`, `exec()`,
  `prepare()`, or any of the `fetch*()` methods.

- **Fetch methods.** The class provides several `fetch*()` methods to reduce
  boilerplate code elsewhere. For example, you can call `fetchAll()` directly
  on the instance instead of having to prepare a statement, bind values,
  execute, and then fetch from the prepared statement. All of the `fetch*()`
  methods take an array of values to bind to to the query statement.

- **Profiler**.  An optional query profiler is provided.

- **Exceptions by default.** `ExendedPdo` starts in the `ERRMODE_EXCEPTION`
  mode for error reporting instead of `ERRMODE_SILENT`.


Overview
--------

The `ExtendedPdo` class is an extension of the PHP-native `PDO` class (Read
more about PDO [here](http://php.net/PDO).) Among other things, this means
that code already using PDO can use ExtendedPdo with no changes.

Instantiation is the same as with PDO:

```php
<?php
use Aura\Sql\Pdo\ExtendedPdo;

$pdo = new ExtendedPdo(
);

?>

Whereas PDO connects on instantiation, ExtendedPdo does not connect immediately.
Instead, it connects only when you call a method that actually needs the
connection to the database; e.g., on `query()`.
```