 <?php
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
// $host = "localhost";
// $user = "umair";
// $db = "web_project";
// $pass = "umair1108";


// // Creates a new pdo object
// $pdo = null;
// try {
// $pdo = new PDO(
//     'mysql:host=' . $host . ';' . 'dbname=' . $db,
//     $user,
//     $pass
// );
// } catch (PDOException) {
//     $pdo = new PDO(
//         'mysql:host=' . $host . ';' . 'dbname=' . $db,
//         "prashant",
//         ""
//     );
// }
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 


// <?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$host = "localhost";
$user = "umair";
$db = "web_project";
$pass = "umair1108";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, show a clean error and exit
    die("Database connection failed: " . $e->getMessage());
}
