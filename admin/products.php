<?php
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

include '../aksi/koneksi.php';

$adminName = $_SESSION['Name'];

$query = "SELECT * FROM products ORDER BY Stock ASC";
$active_filter = 'all';

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    if ($filter === 'display') {
        $query = "SELECT * FROM products ORDER BY Stock ASC";
        $active_filter = 'display';
    } elseif ($filter === 'warehouse') {
        $query = "SELECT products.Name, warehouses.Price, warehouses.Harga_Jual, warehouses.Stock, warehouses.Id, warehouses.Expired_Date, warehouses.Date_Added
                FROM warehouses
                INNER JOIN products ON warehouses.Product_Id = products.Id
                WHERE warehouses.Stock > 0 ";
        $active_filter = 'warehouse';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODUCTS</title>
    <link rel="stylesheet" href="style/products.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Management Produk</h1>
            </div>
            <div class="selection-table">
                <div class="selection-header">
                    <div class="filter-buttons">
                        <a href="?filter=display"
                            class="selection-card <?= $active_filter === 'display' ? 'active' : '' ?>">
                            <p>Display</p>
                        </a>
                        <a href="?filter=warehouse"
                            class="selection-card <?= $active_filter === 'warehouse' ? 'active' : '' ?>">
                            <p>Gudang</p>
                        </a>
                    </div>
                    <div class="add-product">
                        <a href="add_new_product.php">TAMBAH PRODUK BARU</a>
                        <a href="add_stock_product.php">TAMBAH STOK GUDANG</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>HPP</th>
                            <th>Harga Jual</th>
                            <th>Date Added</th>
                            <th>Expired Date</th>
                            <th>Stok</th>
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
                                    <td>Rp <?= number_format($d['Price'], 2) ?></td>
                                    <td>Rp <?= number_format($d['Harga_Jual'], 2) ?></td>
                                    <td><?= htmlspecialchars($d['Date_Added']) ?></td>
                                    <td><?= htmlspecialchars($d['Expired_Date']) ?></td>
                                    <td><?= $d['Stock'] ?></td>
                                    <td class="action-buttons">
                                        <?php if ($active_filter == 'display'): ?>
                                            <a href="update_product.php?Id=<?= $d['Id']; ?>" class="btn edit">Update</a>
                                            <a href="delete_product.php?Id=<?= $d['Id']; ?>" class="btn delete"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus product ini?')">Delete</a>
                                        <?php elseif ($active_filter == 'warehouse'): ?>
                                            <a href="delete_warehouse_stock.php?Id=<?= $d['Id']; ?>" class="btn delete"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus product ini?')">Delete</a>
                                        <?php else: ?>
                                            <a href="update_product.php?Id=<?= $d['Id']; ?>" class="btn edit">Update</a>
                                            <a href="delete_product.php?Id=<?= $d['Id']; ?>" class="btn delete"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus product ini?')">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>