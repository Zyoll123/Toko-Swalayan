<?php
session_start();
include '../aksi/koneksi.php';
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

$adminName = $_SESSION['Name'];

if (!isset($_GET['Id'])) {
    echo "<p>Id Product tidak ditemukan.</p>";
    exit();
}

$Id = (int)$_GET['Id'];

$query_product = "SELECT * FROM products WHERE Id = ?";
$stmt = mysqli_prepare($conn, $query_product);
mysqli_stmt_bind_param($stmt, "i", $Id);
mysqli_stmt_execute($stmt);
$result_product = mysqli_stmt_get_result($stmt);

if (!$result_product || $result_product->num_rows === 0) {
    echo "<p>Data product tidak ditemukan</p>";
    exit();
}

$d = mysqli_fetch_assoc($result_product);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $Price = trim($_POST['Price'] ?? '');
    $Stock = trim($_POST['Stock'] ?? '');
    $Description = trim($_POST['Description'] ?? '');

    $errors = [];
    if (empty($Name))
        $errors[] = "Nama harus diisi";
    if (empty($Price))
        $errors[] = "Price harus diisi";
    if (empty($Stock))
        $errors[] = "Stock harus diisi";
    if (empty($Description))
        $errors[] = "Description harus diisi";

    if (empty($errors)) {
        $query = "UPDATE products SET Name = ?, Price = ?, Stock = ?, Description = ? WHERE Id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sdisi", $Name, $Price, $Stock, $Description, $Id);
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPDATE PRODUCT</title>
    <link rel="stylesheet" href="style/update_product.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Update Product</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" name="Name" id="Name" placeholder="Enter new Name"
                        value="<?= htmlspecialchars($d['Name']) ?>">
                </div>
                <div class="form-group">
                    <input type="number" class="form-input" name="Price" id="Price" placeholder="Enter new price"
                        value="<?= $d['Price'] ?>">
                </div>
                <div class="form-group">
                    <input type="number" class="form-input" name="Stock" id="Stock" placeholder="Enter new stock"
                        value="<?= $d['Stock'] ?>">
                </div>
                <div class="form-group">
                    <input type="text" clang="form-input" name="Description" id="Description"
                        placeholder="Enter new description" value="<?= htmlspecialchars($d['Description']) ?>">
                </div>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</body>

</html>