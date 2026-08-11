<?php
require_once "common.php";
require_login();

$taskId = $_GET['id'] ?? null;
$userId = current_user_id();

if (!$taskId) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$taskId, $userId]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $deleteStmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        $deleteStmt->execute([$taskId, $userId]);
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="confirm-card">
            <h1>Delete Task</h1>
            <p class="confirm-note">Are you sure you want to permanently delete the task "<?php echo e($task['title']); ?>"?</p>
            <form method="POST" action="delete_task.php?id=<?php echo e($taskId); ?>">
                <div class="button-bar">
                    <button class="btn btn-danger" type="submit" name="confirm" value="yes">Yes, delete</button>
                    <button class="btn btn-secondary" type="submit" name="confirm" value="no">No, cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
