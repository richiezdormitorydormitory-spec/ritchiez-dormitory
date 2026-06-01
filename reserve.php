<?php
require_once "config/database.php";
require_once "includes/session.php";

requireLogin();

$user_id = $_SESSION['user_id'];
$room_id = $_GET['room_id'] ?? null;

if (!$room_id) {
    header("Location: rooms.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id=?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header("Location: rooms.php");
    exit;
}

$bedStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM reservations 
    WHERE room_id=? 
    AND status='Approved'
");
$bedStmt->execute([$room_id]);
$occupiedBeds = (int)$bedStmt->fetchColumn();

$availableBeds = (int)$room['capacity'] - $occupiedBeds;

if ($availableBeds <= 0) {
    include "includes/header.php";
    echo "<div class='form-card'><h2>Room Not Available</h2><p>This room is already full.</p><a class='btn gold' href='rooms.php'>Back to Rooms</a></div>";
    include "includes/footer.php";
    exit;
}

$existingStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM reservations 
    WHERE user_id=? 
    AND status IN ('Pending','Approved')
");
$existingStmt->execute([$user_id]);
$existingReservation = (int)$existingStmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn->prepare("
        INSERT INTO reservations(user_id, room_id, move_in_date, occupants, notes)
        VALUES(?,?,?,?,?)
    ")->execute([
        $user_id,
        $room_id,
        $_POST['move_in_date'],
        $_POST['occupants'],
        $_POST['notes']
    ]);

    header("Location: tenant/payment.php");
    exit;
}

include "includes/header.php";
?>

<div class="form-card">
    <h2>Reserve <?=e($room['room_name'])?></h2>

    <p>
        <b>Available Beds:</b>
        <?=$availableBeds?> / <?=$room['capacity']?>
    </p>

    <?php if($existingReservation > 0): ?>
        <div class="alert" style="background:#fff3cd;color:#856404;padding:12px;border-radius:8px;margin-bottom:15px;">
            You already reserved a room. Are you sure you want to reserve another?
        </div>
    <?php endif; ?>

    <form method="POST"
          onsubmit="return confirmReservation(<?=$existingReservation?>);">

        <input type="date" name="move_in_date" required>

        <input
            type="number"
            name="occupants"
            min="1"
            max="<?=$availableBeds?>"
            placeholder="Occupants"
            required
        >

        <textarea name="notes" placeholder="Notes"></textarea>

        <button class="btn gold">Submit</button>
    </form>
</div>

<script>
function confirmReservation(existingReservation){
    if(existingReservation > 0){
        return confirm("You already reserved a room. Are you sure you want to reserve another?");
    }
    return true;
}
</script>

<?php include "includes/footer.php"; ?>