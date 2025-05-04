<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = mysqli_real_escape_string($conn, $_POST['Name']);
    $Email = mysqli_real_escape_string($conn, $_POST['Email']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);

    $query = "INSERT INTO accounts (Name, Email, Role, Password)
            VALUES ('$Name', '$Email', 'Cashier', '$Password')";

    if (mysqli_query($conn, $query)) {
        header("Location: users.php");
        exit;
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
    <title>Document</title>
    <link rel="stylesheet" href="style/add_new_users.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>ADD NEW CASHIER</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" id="name" name="Name" placeholder="Enter Username"
                        oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-input" name="Email" id="email" placeholder="Enter Email" oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-input" name="Password" id="password" placeholder="Enter Password" oninput="toggleLabel(this)" required>
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>
</body>

</html>