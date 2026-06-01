<?php
require_once "../config/database.php";
require_once "../includes/session.php";
requireLogin();

$tenant_id = $_SESSION['user_id'];

$admin = $conn->query("SELECT user_id FROM users WHERE role='admin' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$admin_id = $admin['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$tenant_id, $admin_id, $_POST['message']]);

    header("Location: messages.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT cm.*, u.full_name
    FROM chat_messages cm
    JOIN users u ON cm.sender_id = u.user_id
    WHERE (sender_id = ? AND receiver_id = ?)
       OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$tenant_id, $admin_id, $admin_id, $tenant_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>

<div class="container">
    <h1 class="section-title">Message Admin</h1>

    <div class="table-wrap">
        <?php foreach ($messages as $m): ?>
            <p>
                <strong><?= e($m['full_name']) ?>:</strong>
                <?= e($m['message']) ?><br>
                <small><?= $m['created_at'] ?></small>
            </p>
            <hr>
        <?php endforeach; ?>

        <form method="POST">
            <textarea name="message" required placeholder="Type your message..." style="width:100%;height:100px;"></textarea>
            <br><br>
            <button class="btn" type="submit">Send</button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>