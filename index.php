<?php
require_once "common.php";
require_login();

$filterPriority = $_GET["priority"] ?? "All";
$filterStatus = $_GET["status"] ?? "All";
$userId = current_user_id();

$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$userId];

if ($filterPriority !== "All" && in_array($filterPriority, ["High", "Medium", "Low"], true)) {
    $query .= " AND priority = ?";
    $params[] = $filterPriority;
}

if ($filterStatus === "Complete") {
    $query .= " AND is_complete = 1";
} elseif ($filterStatus === "Incomplete") {
    $query .= " AND is_complete = 0";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 900px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .completed { text-decoration: line-through; color: #777; }
        .priority-High { color: #b71c1c; font-weight: bold; }
        .priority-Medium { color: #f57c00; }
        .priority-Low { color: #2e7d32; }
        .actions a { margin-right: 8px; }
        .filters { margin-bottom: 16px; }
        .filters form { display: inline-block; margin-right: 12px; }
    </style>
</head>
<body>
    <h1>To-Do List</h1>
    <p>Welcome, <?php echo e(current_user_name()); ?>. <a href="logout.php">Logout</a></p>

    <div class="filters">
        <form method="GET" action="index.php">
            <label>Priority:</label>
            <select name="priority" onchange="this.form.submit()">
                <option value="All" <?php echo $filterPriority === 'All' ? 'selected' : ''; ?>>All</option>
                <option value="High" <?php echo $filterPriority === 'High' ? 'selected' : ''; ?>>High</option>
                <option value="Medium" <?php echo $filterPriority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="Low" <?php echo $filterPriority === 'Low' ? 'selected' : ''; ?>>Low</option>
            </select>
            <label>Status:</label>
            <select name="status" onchange="this.form.submit()">
                <option value="All" <?php echo $filterStatus === 'All' ? 'selected' : ''; ?>>All</option>
                <option value="Complete" <?php echo $filterStatus === 'Complete' ? 'selected' : ''; ?>>Complete</option>
                <option value="Incomplete" <?php echo $filterStatus === 'Incomplete' ? 'selected' : ''; ?>>Incomplete</option>
            </select>
            <button type="submit">Apply</button>
            <a href="index.php">Clear Filters</a>
        </form>
        <a href="task_form.php">Add New Task</a>
    </div>

    <?php if (empty($tasks)): ?>
        <p>No tasks found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr class="<?php echo $task['is_complete'] ? 'completed' : ''; ?>">
                        <td><?php echo e($task['title']); ?></td>
                        <td class="priority-<?php echo e($task['priority']); ?>"><?php echo e($task['priority']); ?></td>
                        <td><?php echo e($task['due_date'] ?: '-'); ?></td>
                        <td><?php echo $task['is_complete'] ? 'Complete' : 'Incomplete'; ?></td>
                        <td class="actions">
                            <a href="task_form.php?id=<?php echo $task['id']; ?>">Edit</a>
                            <a href="delete_task.php?id=<?php echo $task['id']; ?>">Delete</a>
                            <a href="toggle_task.php?id=<?php echo $task['id']; ?>&action=<?php echo $task['is_complete'] ? 'incomplete' : 'complete'; ?>">
                                <?php echo $task['is_complete'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
