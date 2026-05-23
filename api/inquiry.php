<?php
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Ensure the data directory exists
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$dataFile = $dataDir . '/inquiries.json';

// Get POST data
$studentName = $_POST['studentName'] ?? '';
$grade = $_POST['grade'] ?? '';
$dob = $_POST['dob'] ?? '';
$parentName = $_POST['parentName'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';

// Basic validation
if (empty($studentName) || empty($grade) || empty($dob) || empty($parentName) || empty($phone) || empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Load existing data
$existingData = [];
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $existingData = json_decode($json, true) ?: [];
}

// Append new entry
$newEntry = [
    'id' => uniqid(),
    'timestamp' => date('Y-m-d H:i:s'),
    'studentName' => htmlspecialchars($studentName),
    'grade' => htmlspecialchars($grade),
    'dob' => htmlspecialchars($dob),
    'parentName' => htmlspecialchars($parentName),
    'phone' => htmlspecialchars($phone),
    'email' => htmlspecialchars($email)
];
$existingData[] = $newEntry;

// Save data
if (file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => true, 'message' => 'Inquiry saved successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save data.']);
}
?>
