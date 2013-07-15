<?php
namespace Aura\Sql\Pdo;

use PDO;

interface ExtendedPdoInterface
{
    // parent PDO methods
    public function beginTransaction();
    public function commit();
    public function errorCode();
    public function errorInfo();
    public function exec($statement);
    public function getAttribute($attribute);
    public static function getAvailableDrivers();
    public function inTransaction();
    public function lastInsertId($name = null);
    public function prepare($statment, $driver_options = null);
    public function query($statement, $fetch_mode = null, $fetch_arg1 = null, $fetch_arg2 = null);
    public function quote($string, $parameter_type = PDO::PARAM_STR);
    public function rollBack();
    public function setAttribute($attribute, $value);

    // extended methods
    public function bindValues(array $values);
    public function connect();
    public function fetchAll($statement, array $bind_values = []);
    public function fetchCol($statement, array $bind_values = []);
    public function fetchValue($statement, array $bind_values = []);
    public function fetchAssoc($statement, array $bind_values = []);
    public function fetchPairs($statement, array $bind_values = []);
    public function fetchOne($statement, array $bind_values = []);
    public function isConnected();
    public function getBindValues();
    public function getProfiler();
    public function setProfiler(ProfilerInterface $profiler);
}
