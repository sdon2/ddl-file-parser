<?php

require_once(__DIR__ . '/DDLFileParser.php');

$parser = new DDLFileParser();
$result = $parser->parseFile(__DIR__ . '/documents/FS_HCFA_1026347812_IN_C.txt');
var_dump($result);