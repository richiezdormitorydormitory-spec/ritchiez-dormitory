<?php
require_once "../config/database.php";
require_once "../includes/session.php";

requireLogin();

$msg = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $file = '';
    $payment_method = $_POST['payment_method'];
    $reference_number = $_POST['reference_number'] ?? '';

    if($payment_method !== 'Cash'){

        if(isset($_FILES['receipt']) && $_FILES['receipt']['name']){
            $file = time() . '_' . basename($_FILES['receipt']['name']);

            move_uploaded_file(
                $_FILES['receipt']['tmp_name'],
                '../uploads/payments/' . $file
            );
        }else{
            $msg = 'Please upload a receipt for this payment method.';
        }
    }

    if($msg == ''){
        $conn->prepare("
            INSERT INTO payment_uploads
            (user_id, amount, payment_method, reference_number, receipt_file)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([
            $_SESSION['user_id'],
            $_POST['amount'],
            $payment_method,
            $reference_number,
            $file
        ]);

        $msg = 'Payment submitted. Please wait for admin approval.';
    }
}

include "../includes/header.php";
?>

<div class="form-card">
    <h2>Submit Payment</h2>

    <?php if($msg): ?>
        <div class="alert success"><?=$msg?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="number"
            step="0.01"
            name="amount"
            placeholder="Amount"
            required
        >

        <select name="payment_method" id="payment_method" required onchange="toggleReceipt()">
            <option value="">Select Payment Method</option>
            <option value="GCash">GCash</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash">Cash</option>
        </select>

        <div id="referenceBox">
            <input
                name="reference_number"
                id="reference_number"
                placeholder="Reference Number"
            >
        </div>

        <div id="receiptBox">
            <input
                type="file"
                name="receipt"
                id="receipt"
                accept="image/*,.pdf"
            >
        </div>

        <button class="btn gold">
            Submit
        </button>

    </form>
</div>

<script>
function toggleReceipt(){
    const method = document.getElementById('payment_method').value;
    const receiptBox = document.getElementById('receiptBox');
    const receipt = document.getElementById('receipt');
    const referenceBox = document.getElementById('referenceBox');
    const reference = document.getElementById('reference_number');

    if(method === 'Cash'){
        receiptBox.style.display = 'none';
        receipt.removeAttribute('required');

        referenceBox.style.display = 'none';
        reference.removeAttribute('required');
        reference.value = 'Cash Payment';
    }else{
        receiptBox.style.display = 'block';
        receipt.setAttribute('required', 'required');

        referenceBox.style.display = 'block';
        reference.setAttribute('required', 'required');
        reference.value = '';
    }
}

toggleReceipt();
</script>

<?php include "../includes/footer.php"; ?>