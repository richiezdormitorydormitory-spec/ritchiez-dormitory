<?php
require_once "../config/database.php";
require_once "../includes/session.php";
requireAdmin();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] == 'delete') {
        $s = $conn->prepare("SELECT room_id FROM reservations WHERE reservation_id=?");
        $s->execute([$id]);
        $reservation = $s->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $conn->prepare("UPDATE rooms SET status='Available' WHERE room_id=?")
                 ->execute([$reservation['room_id']]);
        }

        $conn->prepare("DELETE FROM reservations WHERE reservation_id=?")->execute([$id]);

        header("Location: reservations.php");
        exit;
    }

    $status = $_GET['action'] == 'approve' ? 'Approved' : 'Rejected';

    $conn->prepare("UPDATE reservations SET status=? WHERE reservation_id=?")
         ->execute([$status, $id]);

    $s = $conn->prepare("SELECT user_id, room_id FROM reservations WHERE reservation_id=?");
    $s->execute([$id]);
    $reservation = $s->fetch(PDO::FETCH_ASSOC);

    if ($reservation) {
        $conn->prepare("INSERT INTO notifications(user_id,message) VALUES(?,?)")
             ->execute([$reservation['user_id'], "Your reservation has been $status."]);

        if ($status == 'Approved') {
            $conn->prepare("UPDATE rooms SET status='Occupied' WHERE room_id=?")
                 ->execute([$reservation['room_id']]);
        }
    }

    header("Location: reservations.php");
    exit;
}

include "../includes/header.php";

$rows = $conn->query("
SELECT reservations.*, users.full_name, rooms.room_name
FROM reservations
JOIN users ON reservations.user_id = users.user_id
JOIN rooms ON reservations.room_id = rooms.room_id
ORDER BY reservation_id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="section-title">Reservations</h1>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Tenant</th>
                <th>Room</th>
                <th>Move-in</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach($rows as $r): ?>
            <tr>
                <td><?=e($r['full_name'])?></td>
                <td><?=e($r['room_name'])?></td>
                <td><?=$r['move_in_date']?></td>
                <td><?=$r['status']?></td>
                <td>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a class="btn"
                           style="background:#f0ad4e;color:white;"
                           href="payments.php?user_id=<?=$r['user_id']?>">
                            💳 Payments
                        </a>

                        <?php if ($r['status'] != 'Approved'): ?>
                            <a class="btn" href="?action=approve&id=<?=$r['reservation_id']?>">
                                Approve
                            </a>
                        <?php endif; ?>

                        <?php if ($r['status'] != 'Rejected'): ?>
                            <a class="btn danger" href="?action=reject&id=<?=$r['reservation_id']?>">
                                Reject
                            </a>
                        <?php endif; ?>

                        <?php if ($r['status'] == 'Rejected'): ?>
                            <a class="btn danger"
                               href="?action=delete&id=<?=$r['reservation_id']?>"
                               onclick="return confirm('Delete this rejected reservation?');">
                                🗑 Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>