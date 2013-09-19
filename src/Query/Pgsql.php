<?php
    /**
     * 
     * Modifies an SQL string **in place** to add a `LIMIT ... OFFSET` clause.
     * 
     * @param string $text The SQL string.
     * 
     * @param int $count The number of rows to return.
     * 
     * @param int $offset Skip this many rows first.
     * 
     * @return void
     * 
     */
    public function limit(&$text, $count, $offset = 0)
    {
        $count = (int) $count;
        if ($count) {
            $text .= "LIMIT $count";
            $offset = (int) $offset;
            if ($offset) {
                $text .= " OFFSET $offset";
            }
        }
    }

    public function getLastInsertIdName($table = null, $col = null)
    {
        return $this->quoteName("{$table}_{$col}_seq");
    }
