<?php
    /**
     * 
     * Modifies an SQL string **in place** to add a `TOP` or 
     * `OFFSET ... FETCH NEXT` clause.
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
        $count  = (int) $count;
        $offset = (int) $offset;

        if ($count && ! $offset) {
            // count, but no offset, so we can use TOP
            $text = preg_replace('/^(SELECT( DISTINCT)?)/', "$1 TOP $count", $text);
        } elseif ($count && $offset) {
            // count and offset, use FETCH NEXT
            $text .= "OFFSET $offset ROWS" . PHP_EOL
                   . "FETCH NEXT $count ROWS ONLY" . PHP_EOL;
        }
    }
