<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['access_token'])) {
    header('Location: google_auth.php');
    exit();
}

$backup_status = file_exists('backup_status.txt') ? file_get_contents('backup_status.txt') : 'inactive';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Google Drive Backup Dashboard</title>
</head>
<body>
    <h1>Google Drive Backup Dashboard</h1>
    <p>Statut du backup : <?php echo $backup_status; ?></p>
    <form action="toggle_backup.php" method="post">
        <input type="submit" value="<?php echo $backup_status == 'active' ? 'Arrêter' : 'Démarrer'; ?> le backup">
    </form>
    <form action="generate_rclone_config.php" method="post">
        <input type="submit" value="Régénérer la configuration rclone">
    </form>
    <form action="execute_backup.php" method="post">
        <input type="submit" value="Exécuter le backup maintenant">
    </form>
</body>
</html>