<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

$adminName = $_SESSION['Name'];

$query = "SELECT * FROM accounts";
$active_filter = "all";

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    if ($filter === 'cashier') {
        $query = "SELECT * FROM accounts WHERE Role = 'Cashier'";
        $active_filter = "cashier";
    } elseif ($filter === 'admin') {
        $query = "SELECT * FROM accounts WHERE Role = 'Admin'";
        $active_filter = "admin";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USERS</title>
    <link rel="stylesheet" href="style/users.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Management User</h1>
            </div>
            <div class="selection-table">
                <div class="selection-header">
                    <div class="filter-buttons">
                        <a href="?filter=all" class="selection-card <?= $active_filter === 'all' ? 'active' : '' ?>">
                            <p>ALL</p>
                        </a>
                        <a href="?filter=admin"
                            class="selection-card <?= $active_filter === 'admin' ? 'active' : '' ?>">
                            <p>Admin</p>
                        </a>
                        <a href="?filter=cashier"
                            class="selection-card <?= $active_filter === 'cashier' ? 'active' : '' ?>">
                            <p>Cashier</p>
                        </a>
                    </div>
                    <div class="add-users">
                        <a href="add_new_admin.php">ADD NEW ADMIN</a>
                        <a href="add_new_cashier.php">ADD NEW CASHIER</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($d = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($d['Name']) ?></td>
                                    <td><?= htmlspecialchars($d['Email']) ?></td>
                                    <td><?= htmlspecialchars($d['Role']) ?></td>
                                    <td class="action-buttons">
                                        <a href="update_users.php?Id=<?= $d['Id']; ?>" class="btn edit">Update</a>
                                        <a href="delete_users.php?Id=<?= $d['Id']; ?>" class="btn delete"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus user ini?')">Delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>;