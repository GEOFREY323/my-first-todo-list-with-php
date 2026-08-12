<?php
require_once 'app_helpers.php';
require_login();

$taskId = $_POST['id'] ?? null;
$userId = current_user_id();

if ($taskId) {
    $stmt = $pdo->prepare('UPDATE tasks SET delete_at = NULL WHERE id = ? AND user_id = ?');
    $stmt->execute([$taskId, $userId]);
    set_flash('Task restored successfully.');
}

header('Location: trash.php');
exit;
