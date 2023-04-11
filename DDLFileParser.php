<?php

class DDLFileParser
{
    protected $headers;

    protected $schema;

    protected $fields = [
        'CLAIM#',
        'OA CLAIMID',
        'PATIENT ID',
        'LAST,FIRST',
        'DOB',
        'FROM DOS',
        'TO DOS',
        'CPT',
        'DIAG',
        'TAX ID',
        'ACCNT#',
        'PHYS.ID',
        'PAYER',
        'ERRORS',
    ];

    protected $table;

    public function parseFile($filename)
    {
        $content = file_get_contents($filename);
        $this->headers = $this->getHeaders($content);
        $this->schema = $this->getSchema($this->headers, $this->fields);
        $this->table = $this->getTable($content);
        $result = $this->parseData($this->fields, $this->schema, $this->table);
        return $result;
    }

    protected function getHeaders($content)
    {
        $matches = [];
        if (preg_match("/^CLAIM.+?ERRORS/m",  $content, $matches)) {
            return array_shift($matches);
        }
        return null;
    }

    protected function getSchema($headers, $fields)
    {
        $schema = [];
        
        foreach ($fields as $field) {
            $schema[$field] = stripos($headers, $field);
        }

        return $schema;
    }

    protected function getTable($content)
    {
        $matches = [];
        if (preg_match("/^CLAIM.+?ERRORS.*(\d+\).*?)=/ms",  $content, $matches)) {
            return array_pop($matches);
        }
        return null;
    }

    protected function parseData($fields, $schema, $table)
    {
        $result = [];
        foreach ($fields as $index => $field) {
            $start_pos = $schema[$field];
            $end_pos = -1;
            $length = -1;
            if ($index < count($fields) - 1) {
                $next_field = $fields[$index + 1];
                $end_pos = $schema[$next_field] - 1;
                $length = $end_pos - $start_pos;
            }
            $extracted = substr($table, $start_pos, $length);
            $result[$field] = rtrim($extracted);
        }
        return $result;
    }
}
