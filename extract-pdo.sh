#!/bin/bash
rm -rf Aura.Sql.Pdo

mkdir -p Aura.Sql.Pdo/src/Pdo
mkdir -p Aura.Sql.Pdo/tests/src/Pdo

cp ./autoload.php           Aura.Sql.Pdo/
cp ./src/Pdo/*              Aura.Sql.Pdo/src/Pdo
cp ./tests/phpunit.xml      Aura.Sql.Pdo/tests/
cp ./tests/bootstrap.php    Aura.Sql.Pdo/tests/
cp ./tests/src/Pdo/*        Aura.Sql.Pdo/tests/src/Pdo
