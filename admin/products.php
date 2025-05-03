<?php
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

include '../aksi/koneksi.php';

$adminName = $_SESSION['Name'];

$query = "SELECT * FROM products";
$active_filter = 'all';

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    
    if ($filter === 'display') {
        $query = "SELECT * FROM products";
        $active_filter = 'display';
    } elseif ($filter === 'warehouse') {
        $query = "SELECT * FROM warehouses";
        $active_filter = 'warehouse';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/products.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="selection-table">
                <a href="?filter=display" class="selection-card <?= $active_filter === 'display' ? 'active' : '' ?>">
                    <p>Display</p>
                </a>
                <a href="?filter=warehose" class="selection-card <?= $active_filter === 'warehose' ? 'active' : '' ?>">
                    <p>Warehouse</p>
                </a>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Stock</th>
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
                            <td><?= number_format($d['Price']) ?></td>
                            <td><?= htmlspecialchars($d['Description']) ?></td>
                            <td><?= $d['Stock'] ?></td>
                        </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>No products found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>