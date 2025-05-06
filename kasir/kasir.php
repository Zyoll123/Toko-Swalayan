<?php
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

include '../aksi/koneksi.php';

$kasirName = $_SESSION['Name'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$customer = isset($_GET['customer']) ? $_GET['customer'] : '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $Id = $_POST['Id'];
    $Quantity = $_POST['Quantity'];
    $query_product = mysqli_query($conn, "SELECT * FROM products WHERE Id = '$Id'");
    $result_product = mysqli_fetch_assoc($query_product);

    if ($result_product['Stock'] >= $Quantity) {
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['Id'] == $Id) {
                $new_qty = $item['Quantity'] + $Quantity;
                if ($result_product['Stock'] >= $new_qty) {
                    $_SESSION['cart'][$key]['Quantity'] = $new_qty;
                    $_SESSION['cart'][$key]['Total'] = $result_product['Price'] * $new_qty;
                    $found = true;
                } else {
                    echo "<script>alert('Stock tidak cukup!');</script>";
                    $found = true;
                }
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'Id' => $Id,
                'Name' => $result_product['Name'],
                'Price' => $result_product['Price'],
                'Quantity' => $Quantity,
                'Total' => $result_product['Total'] * $Quantity
            ];
        }
    } else {
        echo "<script>alert('Stock tidak cukup!');</script>";
    }
}

if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <link rel="stylesheet" href="style/kasir.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <h2>Nama Toko</h2>
        </div>
        <div class="kasir-info">
            <span><?php echo htmlspecialchars($kasirName); ?></span>
            <a href="../aksi/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
    <div class="container">

        <div class="content">
            <div class="search-input">
                <input type="text" id="liveSearch" placeholder="Search" name="search"
                    value="<?php echo htmlspecialchars($search) ?>" autocomplete="off" required>
            </div>

            <div class="menu-container">
                <?php
                $query = "SELECT * FROM products";
                if (!empty($search)) {
                    $query .= " WHERE Name LIKE '" . $conn->real_escape_string($search) . "%'";
                }
                $result_product = $conn->query($query);

                if ($result_product && $result_product->num_rows > 0) {
                    while ($row = $result_product->fetch_assoc()) {
                        $format_price = number_format($row['Price'], 0, ',', '.');
                        ?>
                        <div class="menu">
                            <p><?php echo $row['Name'] ?></p>
                            <div class="menu-item">
                                <img src="data:image/jpg;base64,<?php echo base64_encode($row['Image_Product']); ?>"
                                    alt="<?php echo $row['Name']; ?>">
                                <div class="menu-info">
                                    <p>Rp <?php echo $format_price; ?></p>
                                    <p class="stock-product">Stock: <?php echo $row['Stock'] ?></p>
                                    <div class="input-number-container">
                                        <button type="button" class="minusBtn">-</button>
                                        <input type="number" class="number-input" name="quantity[<?php echo $row['Id'] ?>]"
                                            value="1" min="1" max="<?= $row['Stock'] ?>"
                                            price-data="<?php echo $row['Price'] ?>" stock-data="<?= $row['Price'] ?>"
                                            oninput="validateQuantity(this)" required>
                                        <button type="button" class="plusBtn">+</button>
                                    </div>
                                    <div class="added-product-transaction">
                                        <button type="submit" class="addBtn">Add</button>
                                    </div>
                                    <?php


                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Product not found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
        <div class="transaction-info">
            <div class="cart-header">
                <input type="text" placeholder="Customer">
                <p id="liveClock">Loading time...</p>
            </div>
            <div class="cart-items">
                <?php if (empty($_SESSION['cart'])) { ?>
                    <div class="empty-cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <p>Keranjang belanja kosong</p>
                        <small>Tambah produk untuk memulai transaksi</small>
                    </div>
                <?php } else { ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item) { ?>
                        <div class="cart-item">
                            <div class="cart-item-info"><?= $item['Name'] ?></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="submit-transaction">
                <button type="submit">Submit</button>
            </div>
        </div>

    </div>
    <script src="js/kasir.js"></script>
</body>

</html>