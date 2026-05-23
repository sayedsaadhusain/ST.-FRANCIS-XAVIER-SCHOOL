<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$dataFile = $dataDir . '/messages.json';

$msgName = $_POST['msgName'] ?? '';
$msgEmail = $_POST['msgEmail'] ?? '';
$msgSubject = $_POST['msgSubject'] ?? '';
$msgBody = $_POST['msgBody'] ?? '';

if (empty($msgName) || empty($msgEmail) || empty($msgSubject) || empty($msgBody)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$existingData = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $existingData = json_decode($json, true) ?: [];
}

$newEntry = [
    'id' => uniqid(),
    'timestamp' => date('Y-m-d H:i:s'),
    'name' => htmlspecialchars($msgName),
    'email' => htmlspecialchars($msgEmail),
    'subject' => htmlspecialchars($msgSubject),
    'body' => htmlspecialchars($msgBody)
];
$existingData[] = $newEntry;

if (file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Message saved successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save data.']);
}
?>
