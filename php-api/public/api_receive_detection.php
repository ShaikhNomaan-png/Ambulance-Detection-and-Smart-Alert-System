<?php
require_once __DIR__ . '/../config.php';

// Allow CORS for development (restrict in production)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Only accept POST
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(["error" => "POST required"]);
    exit;
}

// DB connect
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

// Handle file upload
$image_path = null;
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
    $uploads_dir = __DIR__ . '/snapshots';
    if(!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);

    $tmp_name = $_FILES['image']['tmp_name'];
    $name = basename($_FILES['image']['name']);
    $target = $uploads_dir . '/' . $name;
    move_uploaded_file($tmp_name, $target);
    $image_path = 'snapshots/'.$name;
}

// read fields (camera should send these)
$camera_id = $conn->real_escape_string($_POST['camera_id'] ?? 'unknown');
$timestamp = $conn->real_escape_string($_POST['timestamp'] ?? date('Y-m-d H:i:s'));
$confidence = floatval($_POST['confidence'] ?? 0);
$bbox = $conn->real_escape_string($_POST['bbox'] ?? '');
$lat = isset($_POST['lat']) && $_POST['lat'] !== '' ? floatval($_POST['lat']) : null;
$lng = isset($_POST['lng']) && $_POST['lng'] !== '' ? floatval($_POST['lng']) : null;

$stmt = $conn->prepare("INSERT INTO detections (camera_id, timestamp, image_path, confidence, bbox, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssddss", $camera_id, $timestamp, $image_path, $confidence, $bbox, $lat, $lng);
# Note: bind types adjusted below depending on nulls.
$stmt = $conn->prepare("INSERT INTO detections (camera_id, timestamp, image_path, confidence, bbox, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdsdd", $camera_id, $timestamp, $image_path, $confidence, $bbox, $lat, $lng);
$stmt->execute();
$detection_id = $stmt->insert_id;
$stmt->close();

echo json_encode(["status" => "ok", "detection_id" => $detection_id]);

// Alerting: find intersections within radius (if lat/lng provided)
$radius_km = 7;
if($lat !== null && $lng !== null){
    $sql = "SELECT id, name, lat, lng, controller_endpoint FROM intersections";
    $res = $conn->query($sql);
    while($row = $res->fetch_assoc()){
        // compute haversine distance in PHP
        $phi1 = deg2rad($lat);
        $phi2 = deg2rad(floatval($row['lat']));
        $dphi = deg2rad(floatval($row['lat']) - $lat);
        $dlambda = deg2rad(floatval($row['lng']) - $lng);
        $a = sin($dphi/2)*sin($dphi/2) + cos($phi1)*cos($phi2)*sin($dlambda/2)*sin($dlambda/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance_km = 6371 * $c;
        if($distance_km <= $radius_km){
            // send to controller (simulate)
            $endpoint = $row['controller_endpoint'];
            if($endpoint){
                $ch = curl_init($endpoint);
                $payload = json_encode(["action" => "clear_lane", "detection_id" => $detection_id]);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $resp = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                // log alert
                $stmt2 = $conn->prepare("INSERT INTO alerts (detection_id, sent_at, method, status, details) VALUES (?, NOW(), ?, ?, ?)");
                $method = 'signal';
                $status = ($code >= 200 && $code < 300) ? 'sent' : 'failed';
                $details = substr($resp ?? '', 0, 1000);
                $stmt2->bind_param("isss", $detection_id, $method, $status, $details);
                $stmt2->execute();
                $stmt2->close();
            }
        }
    }
}

$conn->close();
?>
