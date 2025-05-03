<?php
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

include '../aksi/koneksi.php';

$kasirName = $_SESSION['Name'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$customer = isset($_GET['customer']) ? $_GET['customer'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <link rel="stylesheet" href="style/kasir.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>

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
                                            value="0" min="0" max="100" price-data="<?php echo $row['Price'] ?>" required>
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
            <input type="text" placeholder="Customer">
            <p id="liveClock">Loading time...</p>
            <div class="transaction-content">
                <p>nama produk</p>
                <p>quantity</p>
                <p>subtotal</p>
            </div>
            <div class="submit-transaction">
                <button type="submit">Submit</button>
            </div>
        </div>

    </div>
    <script src="js/kasir.js"></script>
</body>

</html>