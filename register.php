<?php
require "Todo_db.php";
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR name = ?");
        $stmt->execute([$email, $name]);

        if ($stmt->fetch()) {
            $errors[] = "An account with that name or email already exists.";
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $passwordHash]);

        $_SESSION["user_id"] = $pdo->lastInsertId();
        $_SESSION["user_name"] = $name;

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 360px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Register</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="register.php">
        <label>Name</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>

        <label>Email</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br><br>

        <label>Password</label><br>
        <input type="password" name="password"><br><br>

        <label>Confirm Password</label><br>
        <input type="password" name="confirm_password"><br><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
