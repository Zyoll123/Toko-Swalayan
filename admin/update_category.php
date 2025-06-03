<?php
session_start();
include '../aksi/koneksi.php';
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

$adminName = $_SESSION['Name'];

if (!isset($_GET['Id'])) {
    echo "<p>Id Category tidak ditemukan.</p>";
    exit();
}

$Id = (int)$_GET['Id'];

$query_category = "SELECT * FROM categories WHERE Id = ?";
$stmt = mysqli_prepare($conn, $query_category);
mysqli_stmt_bind_param($stmt, "i", $Id);
mysqli_stmt_execute($stmt);
$result_category = mysqli_stmt_get_result($stmt);

if (!$result_category || $result_category->num_rows === 0) {
    echo "<p>Data category tidak ditemukan</p>";
    exit();
}

$d = mysqli_fetch_assoc($result_category);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST['Name'] ?? '');

    $errors = [];
    if (empty($Name))
        $errors[] = "Nama harus diisi";

    if (empty($errors)) {
        $query = "UPDATE categories SET Name = ? WHERE Id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $Name, $Id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: categories.php");
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
    <title>UPDATE CATEGORY</title>
    <link rel="stylesheet" href="style/update_product.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Update Category</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" name="Name" id="Name" placeholder="Enter new Name"
                        value="<?= htmlspecialchars($d['Name']) ?>" required>
                </div>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</body>

</html>