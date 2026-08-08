<?php
require_once "Todo_db.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function current_user_id() {
    return $_SESSION["user_id"] ?? null;
}

function current_user_name() {
    return $_SESSION["user_name"] ?? null;
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
