<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];
$date = date('Y-m-d');

$query_users = $conn->prepare("SELECT COUNT(*) AS total_users FROM accounts WHERE Role = 'Cashier'");
$query_users->execute();
$result_users = $query_users->get_result();
$total_users = $result_users->fetch_assoc()['total_users'] ?? 0;

$query_products = $conn->prepare("SELECT COUNT(*) AS total_products FROM Products WHERE Stock > 0");
$query_products->execute();
$result_products = $query_products->get_result();
$total_products = $result_products->fetch_assoc()['total_products'] ?? 0;

$query_income = $conn->prepare("SELECT SUM(Total) AS income FROM transactions WHERE Transaction_Date = ?");
$query_income->bind_param("s", $date);
$query_income->execute();
$result_income = $query_income->get_result();
$total_income = $result_income->fetch_assoc()['income'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="card-dashboard">
                <div class="dashboard-section">
                    <h2><i class="fa-solid fa-user"></i>Total Users</h2>
                    <div class="card-info">
                        <p><?= $total_users ?></p>
                    </div>
                </div>
                <div class="dashboard-section">
                    <h2><i class="fa-solid fa-box"></i>Total Products</h2>
                    <div class="card-info">
                        <p><?= $total_products ?></p>
                    </div>
                </div>
                <div class="dashboard-section">
                    <h2><i class="fa-solid fa-money-bill"></i>Total Income</h2>
                    <div class="card-info">
                        <p><?= $total_income ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>