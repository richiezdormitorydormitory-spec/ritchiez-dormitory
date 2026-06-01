<?php
require_once "../config/database.php";
require_once "../includes/session.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && ($password === "password" || password_verify($password, $user["password"]))) {
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "admin") {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../tenant/dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid login.";
    }
}

include "../includes/header.php";
?>

<div class="form-card">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button class="btn">Login</button>
    </form>

    <p>Admin: <b>admin@ritchiez.com</b> / <b>password</b></p>
</div>

<?php include "../includes/footer.php"; ?>