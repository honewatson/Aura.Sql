Aura.Sql
========

The Aura.Sql package provides libraries for connecting to and querying against
SQL databases such as MySQL, PostgreSQL, and Sqlite. It includes Query Objects
for select/insert/update/delete, a DataMapper implementation, and
schema-discovery functionality.

For the convenience of those who only want an extended version of PDO, the PDO
portions of this package are available separately under the `Aura.Sql.Pdo`
package. Note that there are no dependencies between the two packages; the
`Aura.Sql.Pdo` package is merely an extracted copy of the PDO library
contained in this package.

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Unlike other Aura pacakges, the README instructions are separated into four
separate documents:

- [README-PDO.md][] for the extended PDO functionality;

- [README-CONNECTION.md][] for the driver-specific connection libraries;

- [README-SCHEMA.md][] for the driver-specific schema discovery libraries;

- [README-QUERY.md][] for the object-oriented query system; and

- [README-MAPPER.md][] for the DataMapper implementation.

[README-PDO.md]: README-PDO.md
[README-CONNECTION.md]: README-CONNECTION.md
[README-SCHEMA.md]: README-SCHEMA.md
[README-QUERY.md]: README-QUERY.md
[README-MAPPER.md]: README-MAPPER.md
