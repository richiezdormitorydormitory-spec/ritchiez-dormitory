<?php
require_once "../config/database.php";
require_once "../includes/session.php";
requireAdmin();

$admin_id = $_SESSION['user_id'];
$tenant_id = $_GET['tenant_id'] ?? null;

if (!$tenant_id) {
    $tenants = $conn->query("SELECT user_id, full_name, email FROM users WHERE role='tenant' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

    include "../includes/header.php";
    ?>

    <div class="container">
        <h1 class="section-title">Tenant Messages</h1>

        <div class="table-wrap">
            <?php foreach($tenants as $t): ?>
                <p>
                    <a class="btn" href="chat.php?tenant_id=<?= $t['user_id'] ?>">
                        <?= e($t['full_name']) ?> (<?= e($t['email']) ?>)
                    </a>
                </p>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    include "../includes/footer.php";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $stmt = $conn->prepare(
        "INSERT INTO chat_messages (sender_id, receiver_id, message)
         VALUES (?, ?, ?)"
    );
    $stmt->execute([$admin_id, $tenant_id, $_POST['message']]);

    header("Location: chat.php?tenant_id=".$tenant_id);
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
$stmt->execute([$admin_id, $tenant_id, $tenant_id, $admin_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>

<div class="container">
    <h1 class="section-title">Chat with Tenant</h1>

    <div class="table-wrap">
        <?php foreach($messages as $m): ?>
            <p>
                <strong><?= e($m['full_name']) ?>:</strong>
                <?= e($m['message']) ?><br>
                <small><?= $m['created_at'] ?></small>
            </p>
            <hr>
        <?php endforeach; ?>

        <form method="POST">
            <textarea name="message"
                      required
                      placeholder="Type your reply..."
                      style="width:100%;height:100px;"></textarea>

            <br><br>

            <button class="btn" type="submit">
                Send Reply
            </button>
        </form>
    </div>
</div>

<?php include "../includes/footer.php"; ?>