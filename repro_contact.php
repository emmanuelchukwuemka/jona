<?php
// repro_contact.php
// Mock a form submission to actions/contact.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_POST = [
    'firstname' => 'Test',
    'lastname' => 'User',
    'email' => 'test@example.com',
    'phone' => '1234567890',
    'comments' => 'This is a test message.'
];

include 'actions/contact.php';
?>
