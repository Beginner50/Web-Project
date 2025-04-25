<?php
session_start();
require_once '../../connect.php';

if (isset($_GET['userID']) && isset($_SESSION['UserID'])) {
    $targetUserID = $_GET['userID']; // the user being verified
    $adminID = $_SESSION['UserID'];  // the admin performing verification

    $stmt = $pdo->prepare('UPDATE approval SET AdminID = ?, IsApproved = ? WHERE UserID = ?');
    $success = $stmt->execute([$adminID, 1, $targetUserID]);

    if ($success) {
        $_SESSION['verifyStatus'] = "User successfully verified.";
    } else {
        $_SESSION['verifyStatus'] = "Failed to verify user.";
    }
} else {
    $_SESSION['verifyStatus'] = "Missing data to verify.";
}

// Redirect back to the admin dashboard
header("Location: /dashboard");
exit();
?>
