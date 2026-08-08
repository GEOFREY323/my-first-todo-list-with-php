<?php
require_once "common.php";
require_login();

$taskId = $_GET['id'] ?? null;
action:
$action = $_GET['action'] ?? null;
$userId = current_user_id();

if (!$taskId || !in_array($action, ['complete', 'incomplete'], true)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$taskId, $userId]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: index.php');
    exit;
}

$isComplete = $action === 'complete' ? 1 : 0;
$toggleStmt = $pdo->prepare('UPDATE tasks SET is_complete = ? WHERE id = ? AND user_id = ?');
$toggleStmt->execute([$isComplete, $taskId, $userId]);

header('Location: index.php');
exit;
