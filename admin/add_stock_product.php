<?php
session_start();
include '../aksi/koneksi.php';
if(!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stock = trim($_POST['Stock'] ?? '');
    $product_id = trim($_POST['Product_Id'] ?? '');
    $price = mysqli_real_escape_string($conn, $_POST['Price'] ?? '');
    $expired_date = !empty($_POST['Expired_Date']) ? $_POST['Expired_Date'] : null;
    $Harga_Jual = $price + ($price * 0.1);

    $errors = [];
    if (empty($stock))
        $errors[] = "Stock harus diisi";
    if (empty($product_id))
        $errors[] = "Product harus diisi";

    if (empty($errors)) {
        $date = date("Y-m-d");
        // $expired_date = date("Y-m-d", strtotime("+1 year", strtotime($date)));

        $query = "INSERT INTO warehouses (Stock, Price, Harga_Jual, Date_Added, Expired_Date, Product_Id)
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
$query = "INSERT INTO warehouses (Stock, Price, Harga_Jual, Date_Added, Expired_Date, Product_Id)
          VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if ($expired_date === null) {
    // Bind null as a param using "s" and pass NULL
    mysqli_stmt_bind_param($stmt, "iiissi", $stock, $price, $Harga_Jual, $date, $expired_date, $product_id);
    mysqli_stmt_send_long_data($stmt, 4, null); // optional but safe
} else {
    mysqli_stmt_bind_param($stmt, "iiissi", $stock, $price, $Harga_Jual, $date, $expired_date, $product_id);
}

        if (mysqli_stmt_execute($stmt)) {
            header("Location: products.php");
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

$query_products = "SELECT * FROM products";
$result_products = mysqli_query($conn, $query_products);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock Product</title>
    <link rel="stylesheet" href="style/add_stock_product.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php'; ?>
        <div class="content">
            <div class="title">
                <h1>TAMBAH STOK GUDANG</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <label for="product_id">Choose Product</label>
                    <select name="Product_Id" id="product_id" required>
                        <option value="">--Choose Product--</option>
                        <?php
                        while ($row = mysqli_fetch_assoc($result_products)) {
                            echo '<option value="' . $row['Id'] . '">' .htmlspecialchars($row['Name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="number" class="form_input" name="Price" id="price" placeholder="Enter Price" required>
                </div>
                <div class="form-group">
                    <label for="expired_date">Input Expired Date</label>
                    <input type="date" class="form_input" name="Expired_Date" id="expired_date" placeholder="Input Expired Date">
                </div>
                <div class="form-group">
                    <input type="number" class="form_input" name="Stock" id="stock" placeholder="Enter Stock" required>
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>
</body>
</html>