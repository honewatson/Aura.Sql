<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @package Aura.Sql
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Sql\Pdo;

use Exception;
use PDOStatement;

/**
 * 
 * Retains query profiles.
 * 
 * @package Aura.Sql
 * 
 */
class Profiler implements ProfilerInterface
{
    /**
     * 
     * Is the profiler active?
     * 
     * @var bool
     * 
     */
    protected $active = false;

    /**
     *
     * Retained profiles.
     *
     * @var array
     *
     */
    protected $profiles = [];

    /**
     * 
     * Turns the profiler on and off.
     * 
     * @param bool $active True to turn on, false to turn off.
     * 
     * @return void
     * 
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }

    /**
     *
     * Is the profiler active?
     * 
     * @return bool
     * 
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * 
     * Executes a PDOStatement and profiles it.
     *
     * @return mixed
     * 
     */
    public function addProfile(
        $duration,
        $function,
        $statement,
        array $bind_values = []
    ) {
        if (! $this->isActive()) {
            return;
        }

        $e = new Exception;
        $this->profiles[] = [
            'duration'    => $duration,
            'function'    => $function,
            'statement'   => $statement,
            'bind_values' => $bind_values,
            'trace'       => $e->getTraceAsString(),
        ];
    }

    /**
     *
     * Returns all the profiles.
     *
     * @return array
     *
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
