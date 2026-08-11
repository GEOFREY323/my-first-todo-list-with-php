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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header-row">
            <div>
                <h1>My Tasks</h1>
                <p class="page-intro">Track your tasks with priority, due dates, and completion status.</p>
            </div>
            <div class="button-bar">
                <a class="btn btn-secondary" href="logout.php">Logout</a>
                <a class="btn btn-primary" href="task_form.php">Add New Task</a>
            </div>
        </div>

        <div class="page-card">
            <div class="panel-header">
                <div>
                    <h2>Welcome, <?php echo e(current_user_name()); ?></h2>
                    <p class="footer-note">Use the filters to show only the tasks you want to focus on.</p>
                </div>
            </div>

            <div class="filters">
                <form method="GET" action="index.php">
                    <label>Priority</label>
                    <select name="priority" onchange="this.form.submit()">
                        <option value="All" <?php echo $filterPriority === 'All' ? 'selected' : ''; ?>>All</option>
                        <option value="High" <?php echo $filterPriority === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Medium" <?php echo $filterPriority === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Low" <?php echo $filterPriority === 'Low' ? 'selected' : ''; ?>>Low</option>
                    </select>
                    <label>Status</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="All" <?php echo $filterStatus === 'All' ? 'selected' : ''; ?>>All</option>
                        <option value="Complete" <?php echo $filterStatus === 'Complete' ? 'selected' : ''; ?>>Complete</option>
                        <option value="Incomplete" <?php echo $filterStatus === 'Incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                    </select>
                    <button class="btn btn-secondary" type="submit">Apply</button>
                    <a class="button-link" href="index.php">Clear Filters</a>
                </form>
            </div>

            <div class="table-card">
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
                                    <td><span class="priority-pill priority-<?php echo e($task['priority']); ?>"><?php echo e($task['priority']); ?></span></td>
                                    <td><?php echo e($task['due_date'] ?: '-'); ?></td>
                                    <td><?php echo $task['is_complete'] ? 'Complete' : 'Incomplete'; ?></td>
                                    <td class="task-actions">
                                        <a class="button-link" href="task_form.php?id=<?php echo $task['id']; ?>">Edit</a>
                                        <a class="button-link" href="delete_task.php?id=<?php echo $task['id']; ?>">Delete</a>
                                        <a class="button-link" href="toggle_task.php?id=<?php echo $task['id']; ?>&action=<?php echo $task['is_complete'] ? 'incomplete' : 'complete'; ?>">
                                            <?php echo $task['is_complete'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
