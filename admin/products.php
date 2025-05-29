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
        $query = "SELECT products.Name, products.Price, products.Description, warehouses.Stock, warehouses.Id
                FROM warehouses
                INNER JOIN products ON warehouses.Product_Id = products.Id
                WHERE warehouses.Stock > 0 ";
        $active_filter = 'warehouse';
    } elseif ($filter === 'category') {
        $query = "SELECT * FROM categories";
        $active_filter = 'category';
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
            <div class="selection-table">
                <div class="selection-header">
                    <div class="filter-buttons">
                        <a href="?filter=display"
                            class="selection-card <?= $active_filter === 'display' ? 'active' : '' ?>">
                            <p>Display</p>
                        </a>
                        <a href="?filter=warehouse"
                            class="selection-card <?= $active_filter === 'warehouse' ? 'active' : '' ?>">
                            <p>Warehouse</p>
                        </a>
                        <a href="?filter=category"
                            class="selection-card <?= $active_filter === 'category' ? 'active' : '' ?>">
                            <p>Categories</p>
                        </a>
                    </div>
                    <div class="add-product">
                        <a href="add_stock_product.php">ADD PRODUCT</a>
                        <a href="add_new_product.php">ADD NEW PRODUCT</a>
                        <a href="add_new_category.php">ADD NEW CATEGORY</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <?php if ($active_filter == 'category'): ?>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Edit</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th>Stock</th>
                                <th>Edit</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            while ($d = $result->fetch_assoc()) {
                                ?>
                                <?php if ($active_filter == 'category'): ?>
                                    <tr>
                                        <td><?= $d['Id'] ?></td>
                                        <td><?= htmlspecialchars($d['Name']) ?></td>
                                        <td class="action-buttons">
                                            <a href="update_category.php?Id=<?= $d['Id'] ?>" class="btn edit">Update</a>
                                            <a href="delete_category.php?Id=<?= $d['Id'] ?>" class="btn delete">Delete</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td><?= $d['Id'] ?></td>
                                        <td><?= htmlspecialchars($d['Name']) ?></td>
                                        <td><?= number_format($d['Price'], 2) ?></td>
                                        <td><?= htmlspecialchars($d['Description']) ?></td>
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
                                <?php endif; ?>
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