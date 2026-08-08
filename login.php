<?php
require "Todo_db.php";
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$loginValue = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginValue = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($loginValue)) {
        $errors[] = "Email or username is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, name, password_hash FROM users WHERE email = ? OR name = ?");
        $stmt->execute([$loginValue, $loginValue]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: index.php");
            exit;
        }

        $errors[] = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 360px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Login</h1>

    <?php foreach ($errors as $error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="login.php">
        <label>Email or Username</label><br>
        <input type="text" name="login" value="<?php echo htmlspecialchars($loginValue); ?>"><br><br>

        <label>Password</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>
