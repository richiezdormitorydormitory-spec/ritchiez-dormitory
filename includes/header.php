<?php require_once __DIR__ . "/session.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RITCHIE'Z Dormitory</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/ritchiez_complete_upgrade/assets/css/style.css">
</head>
<body>

<nav>
    <div class="logo">
        <img src="/ritchiez_complete_upgrade/assets/images/logo.png"
             alt="RITCHIE'Z Dorm"
             style="height:120px; width:auto;">
    </div>

    <ul>
        <li><a href="/ritchiez_complete_upgrade/index.php">Home</a></li>
        <li><a href="/ritchiez_complete_upgrade/rooms.php">Rooms</a></li>
        <li><a href="/ritchiez_complete_upgrade/products.php">Shop</a></li>
        <li><a href="/ritchiez_complete_upgrade/contact.php">Contact</a></li>

        <?php if (isLoggedIn()): ?>

            <?php if (isAdmin()): ?>
                <li><a href="/ritchiez_complete_upgrade/admin/dashboard.php">Admin</a></li>
                <li><a href="/ritchiez_complete_upgrade/admin/chat.php">Messages</a></li>
            <?php else: ?>
                <li><a href="/ritchiez_complete_upgrade/tenant/dashboard.php">Dashboard</a></li>
                <li><a href="/ritchiez_complete_upgrade/tenant/messages.php">Messages</a></li>
            <?php endif; ?>

            <li><a href="/ritchiez_complete_upgrade/auth/logout.php">Logout</a></li>

        <?php else: ?>

            <li><a href="/ritchiez_complete_upgrade/auth/login.php">Login</a></li>
            <li><a href="/ritchiez_complete_upgrade/auth/register.php">Register</a></li>

        <?php endif; ?>
    </ul>
</nav>