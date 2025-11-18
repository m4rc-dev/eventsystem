<?php
include 'db.php';

// ADD event (no evCode input now)
if (isset($_POST['add'])) {
    $evName = $_POST['evName'];
    $evDate = $_POST['evDate'];
    $evVenue = $_POST['evVenue'];
    $evFee  = $_POST['evFee'];

    $conn->query("INSERT INTO Events (evName, evDate, evVenue, evRFee)
                  VALUES ('$evName', '$evDate', '$evVenue', '$evFee')");
}

// EDIT event
if (isset($_POST['edit'])) {
    $id      = $_POST['id'];
    $evName  = $_POST['evName'];
    $evDate  = $_POST['evDate'];
    $evVenue = $_POST['evVenue'];
    $evFee   = $_POST['evFee'];

    $conn->query("UPDATE Events SET
                    evName='$evName',
                    evDate='$evDate',
                    evVenue='$evVenue',
                    evRFee='$evFee'
                  WHERE evCode=$id");
}

// DELETE event
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Events WHERE evCode=$id");
}

// EDIT mode check
$edit_mode = false;
$edit_event = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $edit_event = $conn->query("SELECT * FROM Events WHERE evCode=$id")->fetch_assoc();
}

// SEARCH
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search != "") {
    $events = $conn->query("SELECT * FROM Events
                            WHERE evName LIKE '%$search%'
                               OR evVenue LIKE '%$search%'
                            ORDER BY evCode ASC");
} else {
    $events = $conn->query("SELECT * FROM Events ORDER BY evCode ASC");
}
?>
<!DOCTYPE html>
<html>
<head><title>Events Management</title></head>
<body>

<?php if ($edit_mode && $edit_event): ?>

<h2>Edit Event</h2>
<form method="post">
    <input type="hidden" name="id" value="<?= $edit_event['evCode'] ?>">

    EVENT CODE: <input type="text" value="<?= $edit_event['evCode'] ?>" disabled><br>
    EVENT NAME: <input type="text" name="evName" value="<?= $edit_event['evName'] ?>" required><br>
    EVENT DATE: <input type="date" name="evDate" value="<?= $edit_event['evDate'] ?>" required><br>
    VENUE: <input type="text" name="evVenue" value="<?= $edit_event['evVenue'] ?>" required><br>
    REG FEE: <input type="number" step="0.01" name="evFee" value="<?= $edit_event['evRFee'] ?>" required><br>

    <button type="submit" name="edit">Update Event</button>
    <a href="events.php">Cancel</a>
</form>

<?php else: ?>

<h2>Add Event</h2>
<form method="post">
    <!-- Event code is auto-generated; no input -->
    EVENT NAME: <input type="text" name="evName" required><br>
    EVENT DATE: <input type="date" name="evDate" required><br>
    VENUE: <input type="text" name="evVenue" required><br>
    REG FEE: <input type="number" step="0.01" name="evFee" required><br>

    <button type="submit" name="add">Add Event</button>
</form>

<?php endif; ?>

<form method="get">
    <input type="text" name="search" placeholder="Search events..." value="<?= $search ?>">
    <button type="submit">Search</button>
    <a href="events.php">Clear</a>
</form>

<table border="1" cellpadding="6">
<tr>
    <th>Code</th>
    <th>Name</th>
    <th>Date</th>
    <th>Venue</th>
    <th>Fee</th>
    <th>Actions</th>
</tr>

<?php while ($row = $events->fetch_assoc()): ?>
<tr>
    <td><?= $row['evCode'] ?></td>
    <td><?= $row['evName'] ?></td>
    <td><?= $row['evDate'] ?></td>
    <td><?= $row['evVenue'] ?></td>
    <td><?= $row['evRFee'] ?></td>
    <td>
        <a href="?edit=<?= $row['evCode'] ?>">Edit</a>
        <a href="?delete=<?= $row['evCode'] ?>" onclick="return confirm('Delete event?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
