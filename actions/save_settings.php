<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$settingsFile = '../settings.json';
$current = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];

$current['hero_title'] = $_POST['hero_title'] ?? $current['hero_title'] ?? '';
$current['contact_email'] = $_POST['contact_email'] ?? $current['contact_email'] ?? '';
$current['maintenance_mode'] = $_POST['maintenance_mode'] ?? '0';

if (file_put_contents($settingsFile, json_encode($current, JSON_PRETTY_PRINT))) {
    echo json_encode(['status' => 'success', 'message' => '✓ Settings saved successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save settings.']);
}
