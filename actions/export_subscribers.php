<?php
session_start();

// Security check: only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /admin/index.php");
    exit;
}

require_once '../includes/config.php';

// Ensure the table exists before querying just in case
try {
    $subscribers = $pdo->query("SELECT email, status, created_at FROM subscribers ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $subscribers = [];
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sccdr_subscribers_' . date('Ymd_His') . '.csv"');
$output = fopen('php://output', 'w');

fputcsv($output, ['Email', 'Status', 'Date Subscribed']);

foreach ($subscribers as $row) {
    fputcsv($output, [
        $row['email'],
        $row['status'],
        date('Y-m-d H:i:s', strtotime($row['created_at']))
    ]);
}
fclose($output);
exit;
