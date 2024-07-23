<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['access_token'])) {
    header('Location: google_auth.php');
    exit();
}

$backup_status = file_exists('backup_status.txt') ? file_get_contents('backup_status.txt') : 'inactive';
$new_status = $backup_status == 'active' ? 'inactive' : 'active';
file_put_contents('backup_status.txt', $new_status);

if ($new_status == 'active') {
    // Démarrer le cron job pour le backup quotidien
    exec('(crontab -l ; echo "0 0 * * * /usr/bin/php ' . __DIR__ . '/execute_backup.php") | crontab -');
} else {
    // Arrêter le cron job
    exec('crontab -l | grep -v "' . __DIR__ . '/execute_backup.php" | crontab -');
}

header('Location: dashboard.php');