<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
session_unset();
session_destroy();

header("Location: ../../index.php");
exit();

?>

