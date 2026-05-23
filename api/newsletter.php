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

$dataFile = $dataDir . '/newsletter.json';

$email = $_POST['email'] ?? '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid email address is required.']);
    exit;
}

$existingData = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $existingData = json_decode($json, true) ?: [];
}

// Check for duplicates
foreach ($existingData as $entry) {
    if ($entry['email'] === $email) {
        echo json_encode(['success' => true, 'message' => 'Already subscribed.']);
        exit;
    }
}

$newEntry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'email' => htmlspecialchars($email)
];
$existingData[] = $newEntry;

if (file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Subscribed successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save data.']);
}
?>
