<?php
/* Require once directive to ensure that connect.php is not executed again.

   The benefit here is that after running once, the database connection is already
   stored in the server. 

   Subsequent inclusion of the connect.php file by other php files will 
   not cause it to execute again. Thus, the database connection is established
   only once, meaning bigger performance gain, meaning more money saved 🤑🤑
*/
require_once '../connect.php';

/*
    Again, the states of global variables are already stored in the php server 
    and are preserved throughout further requests of the php file.
    
    Here, since the classes list will not change for the forseeable future, it
    is not necessary to fetch data from the database again.

    For data that is to change, only cache the prepared statement but execute it
    when the php file is requested again.
*/
$stmt = $pdo->prepare('SELECT SubjectCode, Level, ClassGroup FROM class;');
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
