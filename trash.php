<?php
require_once 'app_helpers.php';
require_login();

$message = get_flash();
$userId = current_user_id();

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = ? AND delete_at IS NOT NULL ORDER BY delete_at DESC');
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trash</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-row">
            <div>
                <h1>Trash</h1>
                <p class="page-intro">Restore tasks that were deleted earlier.</p>
            </div>
            <div class="button-bar">
                <a class="btn btn-secondary" href="index.php">Back to Tasks</a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="success-message"><?php echo e($message); ?></div>
        <?php endif; ?>

        <div class="table-card">
            <?php if (empty($tasks)): ?>
                <p>No trashed tasks found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?php echo e($task['title']); ?></td>
                                <td><?php echo e($task['delete_at']); ?></td>
                                <td>
                                    <form method="POST" action="restore_task.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo e($task['id']); ?>">
                                        <button class="btn btn-primary" type="submit">Restore</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
