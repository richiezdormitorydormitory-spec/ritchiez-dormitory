<?php
require_once "../config/database.php";
require_once "../includes/session.php";
requireAdmin();

if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $s = $conn->prepare("SELECT receipt_file FROM payment_uploads WHERE payment_id=?");
    $s->execute([$id]);
    $payment = $s->fetch(PDO::FETCH_ASSOC);

    if($payment){
        if(!empty($payment['receipt_file'])){
            $filePath = "../uploads/payments/" . $payment['receipt_file'];

            if(file_exists($filePath)){
                unlink($filePath);
            }
        }

        $conn->prepare("DELETE FROM payment_uploads WHERE payment_id=?")
             ->execute([$id]);
    }

    header("Location: payments.php");
    exit;
}

if(isset($_GET['status'])){
    $conn->prepare("UPDATE payment_uploads SET status=? WHERE payment_id=?")
         ->execute([$_GET['status'], $_GET['id']]);

    $s = $conn->prepare("SELECT user_id FROM payment_uploads WHERE payment_id=?");
    $s->execute([$_GET['id']]);
    $uid = $s->fetchColumn();

    $conn->prepare("INSERT INTO notifications(user_id,message) VALUES(?,?)")
         ->execute([$uid, "Your payment has been ".$_GET['status']."."]);

    header("Location: payments.php");
    exit;
}

include "../includes/header.php";

$rows = $conn->query("
SELECT payment_uploads.*, users.full_name
FROM payment_uploads
JOIN users ON payment_uploads.user_id = users.user_id
ORDER BY payment_id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1 class="section-title">Payments</h1>

    <div class="table-wrap">
        <table>
            <tr>
                <th>Tenant</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Receipt</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach($rows as $p): ?>
            <tr>
                <td><?=e($p['full_name'])?></td>
                <td>₱<?=number_format($p['amount'],2)?></td>
                <td><?=e($p['payment_method'])?></td>
                <td><?=e($p['reference_number'])?></td>

                <td>
                    <?php if(!empty($p['receipt_file'])): ?>
                        <a href="../uploads/payments/<?=e($p['receipt_file'])?>" target="_blank">
                            View
                        </a>
                    <?php else: ?>
                        No receipt
                    <?php endif; ?>
                </td>

                <td><?=$p['status']?></td>

                <td>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">

                        <?php if($p['payment_method'] != 'Cash' && $p['status'] != 'Verified'): ?>
                            <a class="btn" href="?status=Verified&id=<?=$p['payment_id']?>">
                                Verify
                            </a>
                        <?php endif; ?>

                        <?php if($p['status'] != 'Rejected'): ?>
                            <a class="btn danger" href="?status=Rejected&id=<?=$p['payment_id']?>">
                                Reject
                            </a>
                        <?php endif; ?>

                        <a class="btn danger"
                           style="background:#444;"
                           href="?delete=<?=$p['payment_id']?>"
                           onclick="return confirm('Delete this payment record?');">
                            🗑 Delete
                        </a>

                    </div>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>