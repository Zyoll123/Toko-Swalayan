<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];

$query = "SELECT * FROM categories";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KATEGORI</title>
    <link rel="stylesheet" href="style/products.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Kategori</h1>
            </div>
            <div class="selection-table">
                <div class="selection-header">
                    <div class="add-product">
                        <a href="add_new_category.php">TAMBAH KATEGORI BARU</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
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
                                    <td class="action-buttons">
                                        <a href="update_category.php?Id=<?= $d['Id'] ?>" class="btn edit">Update</a>
                                        <a href="delete_category.php?Id=<?= $d['Id'] ?>" class="btn delete" 
                                        onclick="return confirm('Apakah anda yakin ingin menghapus product ini?')">Delete</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>