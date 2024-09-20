<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include '../connect.php';

// Query the database
$result = $conn->query("SELECT SubjectCode FROM subject;");

// Fetch and return data as json
$values = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $values[] = $row['SubjectCode'];
    }
}

echo json_encode($values);
