<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
session_start();

function refreshGoogleToken($client) {
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
        generateRcloneConfig();
    }
}

function generateRcloneConfig() {
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
}

function executeBackup() {
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setAccessToken($_SESSION['access_token']);

    refreshGoogleToken($client);

    $output = shell_exec('rclone sync googledrive:/ ' . BACKUP_DESTINATION . ' --config=' . RCLONE_CONFIG_PATH);
    
    if (strpos($output, 'Failed to authenticate') !== false) {
        // Gérer l'échec d'authentification
        error_log("Échec d'authentification lors du backup Google Drive");
        return false;
    }

    return true;
}

if (executeBackup()) {
    echo "Backup exécuté avec succès.";
} else {
    echo "Échec du backup.";
}

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('Location: dashboard.php');
}