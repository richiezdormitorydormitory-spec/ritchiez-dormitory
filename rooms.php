<?php
require_once "config/database.php";
include "includes/header.php";

$s = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';
$status = $_GET['status'] ?? '';
$max = $_GET['max_price'] ?? '';

$sql = "
SELECT
    r.*,
    COALESCE(AVG(rv.rating),0) AS avg_rating,
    COUNT(rv.review_id) AS review_count,
    (
        SELECT COUNT(*)
        FROM reservations res
        WHERE res.room_id = r.room_id
        AND res.status = 'Approved'
    ) AS occupied_beds
FROM rooms r
LEFT JOIN reviews rv
    ON r.room_id = rv.room_id
    AND rv.status = 'Visible'
WHERE 1
";

$params = [];

if($s){
    $sql .= " AND (
        r.room_name LIKE ?
        OR r.floor LIKE ?
        OR r.amenities LIKE ?
    )";

    $params[] = "%$s%";
    $params[] = "%$s%";
    $params[] = "%$s%";
}

if($type){
    $sql .= " AND r.room_type = ?";
    $params[] = $type;
}

if($max){
    $sql .= " AND r.price <= ?";
    $params[] = $max;
}

$sql .= " GROUP BY r.room_id";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">

    <h1 class="section-title">Rooms</h1>

    <form class="search-box" method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search room/floor/amenities"
            value="<?= htmlspecialchars($s) ?>"
        >

        <select name="type">
            <option value="">All Types</option>
            <option value="Standard" <?= $type=="Standard" ? "selected" : "" ?>>Standard</option>
            <option value="Premium" <?= $type=="Premium" ? "selected" : "" ?>>Premium</option>
            <option value="Deluxe" <?= $type=="Deluxe" ? "selected" : "" ?>>Deluxe</option>
        </select>

        <input
            type="number"
            name="max_price"
            placeholder="Max Price"
            value="<?= htmlspecialchars($max) ?>"
        >

        <button class="btn">
            Search
        </button>

    </form>

    <div class="grid">

        <?php foreach($rooms as $r): ?>

            <?php
            $capacity = (int)$r['capacity'];
            $occupied = (int)$r['occupied_beds'];
            $available = $capacity - $occupied;

            if($available < 0){
                $available = 0;
            }

            $isFull = ($occupied >= $capacity);
            ?>

            <div class="card">

                <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="Room Image">

                <div class="card-body">

                    <h3><?= htmlspecialchars($r['room_name']) ?></h3>

                    <p><?= htmlspecialchars($r['description']) ?></p>

                    <p>
                        <strong>Status:</strong>

                        <?php if($isFull): ?>
                            <span style="color:red;font-weight:bold;">
                                Not Available
                            </span>
                        <?php else: ?>
                            <span style="color:green;font-weight:bold;">
                                Available
                            </span>
                        <?php endif; ?>
                    </p>

                    <p>
                        <strong>Beds:</strong>
                        <?= $occupied ?> / <?= $capacity ?> occupied
                    </p>

                    <p>
                        <strong>Available Beds:</strong>
                        <?= $available ?>
                    </p>

                    <p class="stars">
                        ⭐ <?= number_format($r['avg_rating'],1) ?>
                        (<?= $r['review_count'] ?>)
                    </p>

                    <div class="price">
                        ₱<?= number_format($r['price'],2) ?> / month
                    </div>

                    <div style="display:flex;gap:10px;margin-top:15px;flex-wrap:wrap;">

                        <a
                            class="btn gold"
                            href="room_details.php?id=<?= $r['room_id'] ?>"
                        >
                            View Details
                        </a>

                        <?php if(!$isFull): ?>

                            <a
                                class="btn"
                                href="reserve.php?room_id=<?= $r['room_id'] ?>"
                            >
                                🛏 Reserve Now
                            </a>

                        <?php else: ?>

                            <button
                                class="btn"
                                style="background:gray;cursor:not-allowed;"
                                disabled
                            >
                                Full
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php include "includes/footer.php"; ?>