<?php
require_once "../config/database.php";
require_once "../includes/session.php";

requireLogin();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);

session_destroy();

header("Location: ../index.php");
exit;
?>