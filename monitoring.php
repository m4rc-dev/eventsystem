<?php
include 'db.php';

$events = $conn->query("SELECT * FROM Events ORDER BY evName ASC");

$filter = isset($_GET['evCode']) ? $_GET['evCode'] : "";

if ($filter != "") {
    $regs = $conn->query("
        SELECT r.*, p.partFName, p.partLName, e.evName, e.evRFee
        FROM Registration r
        LEFT JOIN Participants p ON r.partID = p.partID
        LEFT JOIN Events e ON p.evCode = e.evCode
        WHERE e.evCode = $filter
        ORDER BY r.regCode DESC
    ");

    $agg = $conn->query("
        SELECT COUNT(*) AS cnt, SUM(r.regFPaid) AS totalPaid, e.evRFee AS fee
        FROM Registration r
        LEFT JOIN Participants p ON r.partID = p.partID
        LEFT JOIN Events e ON p.evCode = e.evCode
        WHERE e.evCode = $filter
    ")->fetch_assoc();

    $count = $agg['cnt'];
    $sum   = $agg['totalPaid'];
    $fee   = $agg['fee'];
    $discounts = ($count * $fee) - $sum;
}
?>
<!DOCTYPE html>
<html>
<head><title>Monitoring</title></head>
<body>

<h2>Event Registration Monitoring</h2>

<form method="get">
    SELECT EVENT:
    <select name="evCode">
        <option value="">-- choose --</option>
        <?php while ($e = $events->fetch_assoc()): ?>
        <option value="<?= $e['evCode'] ?>" <?= ($filter == $e['evCode']) ? "selected" : "" ?>>
            <?= $e['evName'] ?>
        </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Show</button>
</form>

<?php if ($filter != ""): ?>

<table border="1" cellpadding="6">
<tr>
    <th>Event</th>
    <th>Participant</th>
    <th>Date</th>
    <th>Paid</th>
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

<h3>Totals</h3>
<ul>
    <li>Count: <?= $count ?></li>
    <li>Total Paid: <?= $sum ?></li>
    <li>Discounts: <?= $discounts ?></li>
</ul>

<?php endif; ?>

</body>
</html>
