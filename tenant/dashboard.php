<?php
require_once "../config/database.php";
require_once "../includes/session.php";

requireLogin();

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT r.*, rm.room_name, rm.price
FROM reservations r
JOIN rooms rm ON rm.room_id = r.room_id
WHERE r.user_id=?
ORDER BY r.created_at DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRent = 0;
foreach($reservations as $r){
    if($r['status'] == "Approved"){
        $totalRent += $r['price'];
    }
}

$paymentStmt = $conn->prepare("
SELECT *
FROM payment_uploads
WHERE user_id=?
ORDER BY created_at DESC
");
$paymentStmt->execute([$user_id]);
$payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>

<div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
        <h1 class="section-title" style="margin:0;">Tenant Dashboard</h1>

        <div style="position:relative;">
            <button onclick="toggleSettings()" class="btn" style="padding:10px 16px;">
                ⚙ Settings
            </button>

            <div id="settingsMenu" style="
                display:none;
                position:absolute;
                right:0;
                top:45px;
                background:white;
                border:1px solid #ddd;
                border-radius:10px;
                min-width:180px;
                box-shadow:0 4px 12px rgba(0,0,0,0.15);
                z-index:999;
            ">
                <a href="../auth/logout.php" style="display:block;padding:12px;text-decoration:none;color:black;">
                    Logout
                </a>

                <a href="delete_account.php"
                   onclick="return confirm('Are you sure you want to delete your account permanently?');"
                   style="display:block;padding:12px;text-decoration:none;color:red;">
                    Delete Account
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-cards">
        <div class="dash-card">
            <h3>Monthly Rent</h3>
            <p class="price">₱<?=number_format($totalRent,2)?></p>
        </div>

        <div class="dash-card">
            <h3>Outstanding Balance</h3>
            <p class="price">₱<?=number_format($totalRent,2)?></p>
        </div>
    </div>

    <div style="margin:20px 0;display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn" href="../rooms.php">Browse Rooms</a>
        <a class="btn gold" href="payment.php">Upload Payment</a>
        <a class="btn" href="Messages.php">Messages</a>
    </div>

    <div class="table-wrap">
        <h2>My Reservations</h2>

        <table>
            <tr>
                <th>Room</th>
                <th>Move-in Date</th>
                <th>Monthly Rent</th>
                <th>Status</th>
            </tr>

            <?php foreach($reservations as $r): ?>
            <tr>
                <td><?=htmlspecialchars($r['room_name'])?></td>
                <td><?=$r['move_in_date']?></td>
                <td>₱<?=number_format($r['price'],2)?></td>
                <td><?=$r['status']?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <br>

    <div class="table-wrap">
        <h2>Payment History</h2>

        <table>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>

            <?php if(count($payments) > 0): ?>
                <?php foreach($payments as $payment): ?>
                <tr>
                    <td><?=date('Y-m-d', strtotime($payment['created_at']))?></td>
                    <td>₱<?=number_format($payment['amount'],2)?></td>
                    <td><?=htmlspecialchars($payment['payment_method'])?></td>
                    <td><?=$payment['status']?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;padding:20px;">
                        No payment submitted yet.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

</div>

<script>
function toggleSettings(){
    const menu = document.getElementById('settingsMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}
</script>

<?php include "../includes/footer.php"; ?>