<?php
session_start();
include '../aksi/koneksi.php';
if (!isset($_SESSION['Name'])) {
    include '../login/login.html';
    exit();
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);

    $query = "INSERT INTO categories (Name) VALUES ('$Name')";

    if (mysqli_query($conn, $query)) {
        header("Location: products.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW CATEGORY</title>
    <link rel="stylesheet" href="style/add_new_product.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>TAMBAH KATEGORI BARU</h1>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" class="form-input" id="name" name="Name" placeholder="Enter Category Name"
                        oninput="toggleLabel(this)">
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>

    <script src="js/add_new_product.js"></script>
</body>

</html>