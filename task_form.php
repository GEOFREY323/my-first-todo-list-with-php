<?php
require_once "common.php";
require_login();

$taskId = $_GET['id'] ?? null;
$isEdit = !empty($taskId);
$userId = current_user_id();

$title = '';
$description = '';
$dueDate = '';
$priority = 'Medium';
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $userId]);
    $task = $stmt->fetch();

    if (!$task) {
        header("Location: index.php");
        exit;
    }

    $title = $task['title'];
    $description = $task['description'];
    $dueDate = $task['due_date'];
    $priority = $task['priority'];
}

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
        if ($isEdit) {
            $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $description, $dueDate ?: null, $priority, $taskId, $userId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $description, $dueDate ?: null, $priority]);
        }

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Edit Task' : 'Add Task'; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; }
        .error { color: red; }
        label { display: block; margin-top: 12px; }
    </style>
</head>
<body>
    <h1><?php echo $isEdit ? 'Edit Task' : 'Add New Task'; ?></h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?php echo e($error); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="task_form.php<?php echo $isEdit ? '?id=' . e($taskId) : ''; ?>">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo e($title); ?>" style="width:100%;"><br>

        <label>Description</label>
        <textarea name="description" rows="4" style="width:100%;"><?php echo e($description); ?></textarea><br>

        <label>Due Date</label>
        <input type="date" name="due_date" value="<?php echo e($dueDate); ?>"><br>

        <label>Priority</label>
        <select name="priority">
            <option value="High" <?php echo $priority === 'High' ? 'selected' : ''; ?>>High</option>
            <option value="Medium" <?php echo $priority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
            <option value="Low" <?php echo $priority === 'Low' ? 'selected' : ''; ?>>Low</option>
        </select><br><br>

        <button type="submit"><?php echo $isEdit ? 'Save Changes' : 'Create Task'; ?></button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
