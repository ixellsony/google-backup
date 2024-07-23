<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';

if (!isset($_SESSION['access_token'])) {
    die("Non autorisé");
}

$config = <<<EOT
[googledrive]
type = drive
client_id = {GOOGLE_CLIENT_ID}
client_secret = {GOOGLE_CLIENT_SECRET}
token = {
    "access_token": "{$_SESSION['access_token']['access_token']}",
    "token_type": "Bearer",
    "refresh_token": "{$_SESSION['access_token']['refresh_token']}",
    "expiry": "{$_SESSION['access_token']['expires_in']}"
}
EOT;

file_put_contents(RCLONE_CONFIG_PATH, $config);
echo "Fichier de configuration rclone généré avec succès.";
header('Location: dashboard.php');