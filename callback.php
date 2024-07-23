<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'google_auth.php';

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    header('Location: dashboard.php');
}