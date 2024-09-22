<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
/* Require once directive to ensure that connect.php is not executed again.

   The benefit here is that after running once, the database connection is already
   stored in the web server. 

   Subsequent inclusion of the connect.php file by other php files will 
   not cause it to execute again. Thus, the database connection is established
   only once, meaning bigger performance gain, meaning more money saved 🤑🤑

*/
require_once '../connect.php';

/*
    Preparing $stmt allows the web server to cache the compiled version, while
    also preventing sql injections.

    If you want to omit this step for miniscule performance gains, simply
    declare $stmt as a global variable by pre-fixing it with the global
    keyword and check if it is not null.

    The states of global variables persist in subsequent requests since they
    are stored in the web server.
*/
$stmt = $pdo->prepare('SELECT SubjectCode,SubjectName FROM subject;');
$stmt->execute();

$row = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($row);
