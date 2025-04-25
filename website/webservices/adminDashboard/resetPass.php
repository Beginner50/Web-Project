 <?php
require_once '../../connect.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Set default password (e.g., "$1lent.k") hashed
    $defaultPassword = password_hash('pass1234',  PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("UPDATE user SET Password = ? WHERE UserID = ?");
    if ($stmt->execute([$defaultPassword, $userID])) {
        header("Location: /dashboard?message=Password reset successfully");
        exit();
    } else {
        echo "Error resetting password.";
    }
} else {
    echo "Missing userID.";
}
?>
