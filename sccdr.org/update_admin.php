<?php
require_once 'includes/config.php';

$adminEmail = 'admin@sccdr.org';
$newPass = 'SCCDRMasterAdmin7829Security2026';

$hash = password_hash($newPass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ? AND role = 'admin'");
if ($stmt->execute([$hash, $adminEmail])) {
    echo "SUCCESS: Admin password updated for $adminEmail";
} else {
    echo "ERROR: Failed to update password.";
}
?>
