<?php

// include file config
require '../config/config.php';

// sql file to execute
$sql_execute = 'Salao.sql';

// create a new database connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset('utf8');
// Check if any error occurred
if (mysqli_connect_errno()) {
    die('An error occurred while connecting to the database');
}
$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);

$query = file_get_contents($sql_execute, false, $context);
if (!$query) {
    die('Error opening file');
}
/* execute multi query */


if (mysqli_multi_query($mysqli, $query)) {
    header('location: ../../index.php');
} else {
    header('location: setup-config.php');
}
header('location: ../../index.php');
