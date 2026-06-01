<?php
require_once "../config/database.php";
require_once "../includes/session.php";
requireAdmin();

include "../includes/header.php";

$tenants = $conn->query("SELECT COUNT(*) FROM users WHERE role='tenant'")->fetchColumn();
$rooms = $conn->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$res = $conn->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pay = $conn->query("SELECT COUNT(*) FROM payment_uploads")->fetchColumn();
?>

<div class="container">
    <h1 class="section-title">Admin Dashboard</h1>

    <div class="dashboard-cards">
        <div class="dash-card">
            <h3>Tenants</h3>
            <p><?=$tenants?></p>
        </div>

        <div class="dash-card">
            <h3>Rooms</h3>
            <p><?=$rooms?></p>
        </div>

        <div class="dash-card">
            <h3>Reservations</h3>
            <p><?=$res?></p>
        </div>

        <div class="dash-card">
            <h3>Payments</h3>
            <p><?=$pay?></p>
        </div>
    </div>

    <div class="grid">
        <a class="btn" href="rooms.php">Rooms</a>
        <a class="btn" href="reservations.php">Reservations</a>
        <a class="btn" href="users.php">Users</a>
        <a class="btn" href="payments.php">Payments</a>
        <a class="btn" href="products.php">Products</a>
        <a class="btn" href="orders.php">Orders</a>
        <a class="btn" href="reviews.php">Reviews</a>
        <a class="btn" href="chat.php">Messages</a>
        <a class="btn gold" href="reports.php">Reports</a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>