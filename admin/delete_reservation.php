<?php
require_once "../config/database.php";
require_once "../includes/session.php";

requireAdmin();

if(isset($_GET['id'])){
    $reservation_id = (int)$_GET['id'];

    $stmt = $conn->prepare("DELETE FROM reservations WHERE reservation_id = ?");
    $stmt->execute([$reservation_id]);
}

header("Location: reservations.php");
exit;
?>