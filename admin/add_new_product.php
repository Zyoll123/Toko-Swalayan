<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $Price = mysqli_real_escape_string($conn, $_POST['Price']);
    $Stock = mysqli_real_escape_string($conn, $_POST['Stock']);
    $Description = mysqli_real_escape_string($conn, $_POST['Description']);
    $Category_Id = mysqli_real_escape_string($conn, $_POST['Category_Id']);

    $Expired_Date = date('Y-m-d', strtotime('+1 year'));

    if (isset($_FILES['Image_Product']['tmp_name'])) {
        $image = $_FILES['Image_Product']['tmp_name'];
        $Image_Result = addslashes(file_get_contents($image));
    } else {
        echo "Tidak ada gambar yang diunggah!";
        exit;
    }

    $query = "INSERT INTO Products (Name, Price, Stock, Description, Image_Product, Expired_Date, Category_Id)
        VALUES ('$Name', '$Price', '$Stock', '$Description', '$Image_Result', '$Expired_Date', '$Category_Id')";

    if (mysqli_query($conn, $query)) {
        header("Location: products.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$query_category = "SELECT * FROM categories";
$result_category = mysqli_query($conn, $query_category);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW PRODUCT</title>
    <link rel="stylesheet" href="style/add_new_product.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>ADD NEW PRODUCT</h1>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" class="form-input" id="name" name="Name" placeholder="Enter Product Name"
                        oninput="toggleLabel(this)">
                </div>
                <div class="form-group">
                    <input type="number" class="form-input" name="Price" id="price" placeholder="Enter Price"
                        oninput="toggleLabel(this)">
                </div>
                <div class="form-group">
                    <input type="number" class="form-input" name="Stock" id="stock" placeholder="Enter Stock"
                        oninput="toggleLabel(this)">
                </div>
                <div class="form-group">
                    <input type="text" class="form-input" name="Description" id="description"
                        placeholder="Enter Description" oninput="toggleLabel(this)">
                </div>
                <div class="form-group">
                    <label class="custom-file-upload">
                        Enter Product Image
                        <input type="file" class="file-input" name="Image_Product" id="fileUpload" required />
                    </label>
                    <div class="file-name" id="fileName">File not found!</div>
                </div>
                <div class="form-group">
                    <label for="Category_Id">Choose Category</label>
                    <select name="Category_Id" id="Category_Id" required>
                        <option value="">--Choose Category--</option>
                        <?php
                        while ($row = mysqli_fetch_assoc($result_category)) {
                            echo '<option value="' . $row['Id'] . '">' . htmlspecialchars($row['Name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>

    <script src="js/add_new_product.js"></script>
</body>

</html>