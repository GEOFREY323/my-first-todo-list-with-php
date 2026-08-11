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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-card">
            <div class="header-row">
                <div>
                    <h1>Login</h1>
                    <p class="page-intro">Enter your credentials to access your to-do tasks.</p>
                </div>
                <div class="button-bar">
                    <a class="button-link" href="register.php">Create an account</a>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="input-group">
                    <label>Email or Username</label>
                    <input type="text" name="login" value="<?php echo htmlspecialchars($loginValue); ?>">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password">
                </div>
                <div class="button-bar">
                    <button class="btn btn-primary" type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
