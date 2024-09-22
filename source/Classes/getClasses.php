<?php

require_once '../connect.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$results = $conn->query('SELECT SubjectCode, Level, ClassGroup FROM class;');

echo json_encode($results);
