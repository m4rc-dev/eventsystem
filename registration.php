<?php
include 'db.php';

// Register
if (isset($_POST['register'])) {
    $partID = $_POST['partID'];
    $evCode = $_POST['evCode'];
    $pay    = $_POST['pay'];

    // get participant
    $p = $conn->query("SELECT * FROM Participants WHERE partID=$partID")->fetch_assoc();
    // get event
    $e = $conn->query("SELECT * FROM Events WHERE evCode='$evCode'")->fetch_assoc();

    if ($p && $e) {
        $fee = $e['evRFee'];
        $discount = $fee * $p['partDRate'];
        $paid = $fee - $discount;

        $regDate = date("Y-m-d");

        $conn->query("INSERT INTO Registration (partID, regDate, regFPaid, regPMode)
                      VALUES ($partID, '$regDate', '$paid', '$pay')");
    }
}

// dropdown lists
$parts = $conn->query("SELECT * FROM Participants ORDER BY partFName ASC");
$events = $conn->query("SELECT * FROM Events ORDER BY evName ASC");

// list registrations
$regs = $conn->query("
    SELECT r.*, p.partFName, p.partLName, e.evName
    FROM Registration r
    LEFT JOIN Participants p ON r.partID = p.partID
    LEFT JOIN Events e ON p.evCode = e.evCode
    ORDER BY r.regCode DESC
");
?>
<!DOCTYPE html>
<html>
<head><title>Registration</title></head>
<body>

<h2>Register Participant</h2>
<form method="post">
    PARTICIPANT:
    <select name="partID" required>
        <option value="">-- choose --</option>
        <?php while ($p = $parts->fetch_assoc()): ?>
            <option value="<?= $p['partID'] ?>">
                <?= $p['partFName'] ?> <?= $p['partLName'] ?>
            </option>
        <?php endwhile; ?>
    </select><br>

    EVENT:
    <select name="evCode" required>
        <option value="">-- choose --</option>
        <?php while ($e = $events->fetch_assoc()): ?>
            <option value="<?= $e['evCode'] ?>"><?= $e['evName'] ?></option>
        <?php endwhile; ?>
    </select><br>

    PAYMENT MODE:
    <select name="pay" required>
        <option value="Cash">Cash</option>
        <option value="Card">Card</option>
    </select><br>

    <button type="submit" name="register">Register</button>
</form>

<h2>Registration List</h2>
<table border="1" cellpadding="6">
    <tr>
        <th>Reg Code</th>
        <th>Participant</th>
        <th>Event</th>
        <th>Date</th>
        <th>Amount Paid</th>
        <th>Payment Mode</th>
    </tr>

    <?php while ($r = $regs->fetch_assoc()): ?>
    <tr>
        <td><?= $r['regCode'] ?></td>
        <td><?= $r['partFName'] ?> <?= $r['partLName'] ?></td>
        <td><?= $r['evName'] ?></td>
        <td><?= $r['regDate'] ?></td>
        <td><?= $r['regFPaid'] ?></td>
        <td><?= $r['regPMode'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
