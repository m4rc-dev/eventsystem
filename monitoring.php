<?php
include 'db.php';

// event dropdown
$events = $conn->query("SELECT * FROM Events ORDER BY evName ASC");

// selected event
$filter = isset($_GET['evCode']) ? $_GET['evCode'] : "";

if ($filter != "") {
    // registration list
    $regs = $conn->query("
        SELECT r.*, p.partFName, p.partLName, e.evName, e.evRFee
        FROM Registration r
        LEFT JOIN Participants p ON r.partID = p.partID
        LEFT JOIN Events e ON p.evCode = e.evCode
        WHERE e.evCode = '$filter'
        ORDER BY r.regCode DESC
    ");

    // aggregates
    $agg = $conn->query("
        SELECT COUNT(*) AS cnt, SUM(r.regFPaid) AS totalPaid, e.evRFee AS fee
        FROM Registration r
        LEFT JOIN Participants p ON r.partID = p.partID
        LEFT JOIN Events e ON p.evCode = e.evCode
        WHERE e.evCode = '$filter'
    ")->fetch_assoc();

    $count = $agg['cnt'];
    $sum   = $agg['totalPaid'];
    $fee   = $agg['fee'];
    $discounts = ($count * $fee) - $sum;
}
?>
<!DOCTYPE html>
<html>
<head><title>Event Monitoring</title></head>
<body>

<h2>Event Registration Monitoring</h2>

<form method="get">
    SELECT EVENT:
    <select name="evCode" required>
        <option value="">-- choose --</option>

        <?php while ($e = $events->fetch_assoc()): ?>
        <option value="<?= $e['evCode'] ?>" <?= ($filter == $e['evCode']) ? "selected" : "" ?>>
            <?= $e['evName'] ?>
        </option>
        <?php endwhile; ?>

    </select>
    <button type="submit">Filter</button>
</form>

<?php if ($filter != ""): ?>

<h3>Results for event: <?= $filter ?></h3>

<table border="1" cellpadding="6">
<tr>
    <th>Event Name</th>
    <th>Participant</th>
    <th>Reg Date</th>
    <th>Fee Paid</th>
</tr>

<?php while ($r = $regs->fetch_assoc()): ?>
<tr>
    <td><?= $r['evName'] ?></td>
    <td><?= $r['partFName'] ?> <?= $r['partLName'] ?></td>
    <td><?= $r['regDate'] ?></td>
    <td><?= $r['regFPaid'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<h3>Aggregates</h3>
<ul>
    <li>Count of Records: <?= $count ?></li>
    <li>Total Fees Paid: <?= $sum ?></li>
    <li>Total Discounts: <?= $discounts ?></li>
</ul>

<?php endif; ?>

</body>
</html>
