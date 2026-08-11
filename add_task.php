<?php
require_once 'app_helpers.php';
require_login();

$userId = current_user_id();
$title = '';
$description = '';
$dueDate = '';
$priority = 'Medium';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dueDate = trim($_POST['due_date'] ?? '');
    $priority = $_POST['priority'] ?? 'Medium';

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $priority = 'Medium';
    }

    if ($dueDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = 'Due date must be in YYYY-MM-DD format.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO tasks (user_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $title, $description, $dueDate ?: null, $priority]);

        set_flash('Task added successfully.');
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-row">
            <div>
                <h1>Add New Task</h1>
                <p class="page-intro">Create a task and stay organized.</p>
            </div>
            <div class="button-bar">
                <a class="button-link" href="index.php">Back to Tasks</a>
            </div>
        </div>

        <div class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="add_task.php">
                <div class="input-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo e($title); ?>">
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="description"><?php echo e($description); ?></textarea>
                </div>

                <div class="input-group">
                    <label>Due Date</label>
                    <input type="date" name="due_date" value="<?php echo e($dueDate); ?>">
                </div>

                <div class="input-group">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                </div>

                <div class="button-bar">
                    <button class="btn btn-primary" type="submit">Create Task</button>
                    <a class="btn btn-secondary" href="index.php">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
