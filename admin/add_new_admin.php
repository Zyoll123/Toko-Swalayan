<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Password = trim($_POST['Password'] ?? '');

    $errors = [];
    if (empty($Name))
        $errors[] = "Nama harus diisi";
    if (empty($Email))
        $errors[] = "Email harus diisi";
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Format email tidak valid.";

    if (empty($errors)) {
        $query = "INSERT INTO accounts (Name, Email, Role, Password)
        VALUES (?, ?, 'Admin', ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $Name, $Email, $Password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW ADMIN</title>
    <link rel="stylesheet" href="style/add_new_users.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>ADD NEW ADMIN</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" id="name" name="Name" placeholder="Enter Username"
                        oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-input" name="Email" id="email" placeholder="Enter Email"
                        oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-input" name="Password" id="password" placeholder="Enter Password"
                        oninput="toggleLabel(this)" required>
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>

    <script src="js/add_new_product.js"></script>
</body>

</html>