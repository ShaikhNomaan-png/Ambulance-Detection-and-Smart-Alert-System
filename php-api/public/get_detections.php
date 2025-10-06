<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$res = $conn->query("SELECT * FROM detections ORDER BY timestamp DESC LIMIT 100");
$out = [];
while($row = $res->fetch_assoc()){
    $out[] = $row;
}
$conn->close();
echo json_encode($out);
?>
