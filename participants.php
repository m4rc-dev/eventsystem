<?php
include 'db.php';

// Add participant
if (isset($_POST['add'])) {
    $evCode = $_POST['evCode'];
    $fName  = $_POST['fName'];
    $lName  = $_POST['lName'];
    $dRate  = $_POST['dRate'];

    $conn->query("INSERT INTO Participants (evCode, partFName, partLName, partDRate)
                  VALUES ('$evCode', '$fName', '$lName', '$dRate')");
}

// Edit participant
if (isset($_POST['edit'])) {
    $id    = $_POST['id'];
    $evCode = $_POST['evCode'];
    $fName  = $_POST['fName'];
    $lName  = $_POST['lName'];
    $dRate  = $_POST['dRate'];

    $conn->query("UPDATE Participants SET
                    evCode='$evCode',
                    partFName='$fName',
                    partLName='$lName',
                    partDRate='$dRate'
                  WHERE partID=$id");
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Participants WHERE partID=$id");
}

// Edit mode
$edit_mode = false;
$edit_part = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM Participants WHERE partID=$id");
    $edit_part = $res->fetch_assoc();
}

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search != "") {
    $parts = $conn->query("SELECT * FROM Participants
                           WHERE partFName LIKE '%$search%' 
                              OR partLName LIKE '%$search%'
                           ORDER BY partID ASC");
} else {
    $parts = $conn->query("SELECT * FROM Participants ORDER BY partID ASC");
}

// Events dropdown
$events = $conn->query("SELECT evCode, evName FROM Events ORDER BY evName ASC");
?>
<!DOCTYPE html>
<html>
<head><title>Participants Management</title></head>
<body>

<?php if ($edit_mode && $edit_part): ?>
<h2>Edit Participant</h2>
<form method="post">
    <input type="hidden" name="id" value="<?= $edit_part['partID'] ?>">

    EVENT:
    <select name="evCode">
        <option value="">-- none --</option>
        <?php while ($e = $events->fetch_assoc()): ?>
        <option value="<?= $e['evCode'] ?>" <?= $edit_part['evCode'] == $e['evCode'] ? "selected" : "" ?>>
            <?= $e['evName'] ?> (<?= $e['evCode'] ?>)
        </option>
        <?php endwhile; ?>
    </select><br>

    FIRST NAME: <input type="text" name="fName" value="<?= $edit_part['partFName'] ?>" required><br>
    LAST NAME : <input type="text" name="lName" value="<?= $edit_part['partLName'] ?>" required><br>
    DISCOUNT RATE (e.g. 0.10): <input type="number" step="0.01" name="dRate" value="<?= $edit_part['partDRate'] ?>"><br>

    <button type="submit" name="edit">Update Participant</button>
    <a href="participants.php">Cancel</a>
</form>

<?php else: ?>
<h2>Add Participant</h2>
<form method="post">
    EVENT:
    <select name="evCode">
        <option value="">-- none --</option>
        <?php while ($e = $events->fetch_assoc()): ?>
        <option value="<?= $e['evCode'] ?>"><?= $e['evName'] ?> (<?= $e['evCode'] ?>)</option>
        <?php endwhile; ?>
    </select><br>

    FIRST NAME: <input type="text" name="fName" required><br>
    LAST NAME : <input type="text" name="lName" required><br>
    DISCOUNT RATE: <input type="number" step="0.01" name="dRate"><br>

    <button type="submit" name="add">Add Participant</button>
</form>
<?php endif; ?>

<form method="get">
    <input type="text" name="search" placeholder="Search..." value="<?= $search ?>">
    <button type="submit">Search</button>
    <a href="participants.php">Clear</a>
</form>

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Event</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Discount</th>
    <th>Actions</th>
</tr>

<?php while ($row = $parts->fetch_assoc()): ?>
<tr>
    <td><?= $row['partID'] ?></td>
    <td><?= $row['evCode'] ?></td>
    <td><?= $row['partFName'] ?></td>
    <td><?= $row['partLName'] ?></td>
    <td><?= $row['partDRate'] ?></td>
    <td>
        <a href="?edit=<?= $row['partID'] ?>">Edit</a>
        <a href="?delete=<?= $row['partID'] ?>" onclick="return confirm('Delete this participant?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</body>
</html>
