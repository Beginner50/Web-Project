<?php
// Call the stored procedure
$sGetListUsers = $pdo->prepare('CALL sp_getListUsers()');
$sGetListUsers->execute();

// Fetch the first result set
$users = $sGetListUsers->fetchAll(PDO::FETCH_ASSOC);

// Clear any additional result sets
while ($sGetListUsers->nextRowset()) {
    // Fetch and discard any additional result sets
    $sGetListUsers->fetchAll(PDO::FETCH_ASSOC);
}

// Close the cursor
$sGetListUsers->closeCursor();
