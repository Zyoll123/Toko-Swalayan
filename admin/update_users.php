<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];

if (!isset($_GET['Id'])) {
    echo "<p>Id user tidak ditemukan.</p>";
    exit();
}

$Id = (int)$_GET['Id'];

$query_users = "SELECT * FROM accounts WHERE Id = ?";
$stmt = mysqli_prepare($conn, $query_users);
mysqli_stmt_bind_param($stmt, "i", $Id);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);

if (!$result_users || $result_users->num_rows === 0) {
    echo "<p>Data user tidak ditemukan.</p>";
    exit();
}

$d = mysqli_fetch_assoc($result_users);

$query_roles = "SELECT DISTINCT Role FROM accounts";
$result_roles = mysqli_query($conn, $query_roles);
$roles = [];
while ($row = mysqli_fetch_assoc($result_roles)) {
    $roles[] = $row['Role'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Password = $_POST['Password'] ?? '';
    $Role = trim($_POST['Role'] ?? '');

    $errors = [];
    if (empty($Name)) $errors[] = "Nama harus diisi";
    if (empty($Email)) $errors[] = "Email harus diisi";
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
    if (empty($Role)) $errors[] = "Role harus dipilih";

    if (empty($errors)) {
        $passwordUpdate = '';
        if (!empty($Password)) {
            $passwordUpdate = ", Password = ?";
            $query = "UPDATE accounts SET Name = ?, Email = ?, Role = ? $passwordUpdate WHERE Id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $Name, $Email, $Role, $Password, $Id);
        } else {
            $query = "UPDATE accounts SET Name = ?, Email = ?, Role = ? WHERE Id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $Name, $Email, $Role, $Id);
        }

        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="style/update_users.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Update Users</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" name="Name" id="name" placeholder="Enter new Name"
                        value="<?= htmlspecialchars($d['Name']) ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-input" name="Email" id="email" placeholder="Enter new Email"
                        value="<?= htmlspecialchars($d['Email']) ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-input" name="Password" id="password"
                        placeholder="Enter new password (leave blank to keep current)">
                </div>
                <div class="form-group">
                    <label for="Role">Choose Role</label>
                    <select name="Role" id="Role" required>
                        <option value="">--Choose Role--</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= htmlspecialchars($role) ?>" 
                                <?= ($role === $d['Role']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</body>
</html>