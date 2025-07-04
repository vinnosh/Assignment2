<?php
// Include DB config
define('DB_SERVER', 'crud-db.cr4kozvtstto.us-east-1.rds.amazonaws.com');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', 'admin1234!');
define('DB_DATABASE', 'sample');

// Connect to DB
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $payer = $_POST['payer'];
    $amount = $_POST['amount'];
    $conn->query("INSERT INTO payments (payer, amount) VALUES ('$payer', '$amount')");
    header("Location: PaymentPage.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $payer = $_POST['payer'];
    $amount = $_POST['amount'];
    $conn->query("UPDATE payments SET payer='$payer', amount='$amount' WHERE id=$id");
    header("Location: PaymentPage.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM payments WHERE id=$id");
    header("Location: PaymentPage.php");
    exit;
}

// Fetch record for editing
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM payments WHERE id=$id");
    $edit = $result->fetch_assoc();
}

// Fetch all
$payments = $conn->query("SELECT * FROM payments");

//DB sample:

//CREATE TABLE payments (
    //id INT AUTO_INCREMENT PRIMARY KEY,
    //payer VARCHAR(100),
    //amount DECIMAL(10,2)
//);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Page (Full CRUD)</title>
</head>
<body>
    <h2><?= $edit ? "Edit Payment ID {$edit['id']}" : "Add New Payment" ?></h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
        <input type="text" name="payer" placeholder="Payer Name" required value="<?= $edit['payer'] ?? '' ?>">
        <input type="number" name="amount" placeholder="Amount" required value="<?= $edit['amount'] ?? '' ?>">
        <button type="submit" name="<?= $edit ? 'update' : 'add' ?>"><?= $edit ? 'Update' : 'Add' ?></button>
        <?php if ($edit): ?>
            <a href="PaymentPage.php">Cancel</a>
        <?php endif; ?>
    </form>

    <h2>All Payments</h2>
    <table border="1" cellpadding="6">
        <tr><th>ID</th><th>Payer</th><th>Amount</th><th>Actions</th></tr>
        <?php while($row = $payments->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['payer']) ?></td>
            <td><?= $row['amount'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this payment?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>





