<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
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

$query_chart = $conn->prepare("
SELECT 
DATE(Transaction_Date) AS date, 
SUM(Total) AS daily_income 
FROM transactions 
WHERE Transaction_Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
GROUP BY 
DATE(Transaction_Date) 
ORDER BY 
DATE(Transaction_Date) ASC
");

$query_chart->execute();
$result_chart = $query_chart->get_result();

$chart_labels = [];
$chart_data = [];

while ($row = $result_chart->fetch_assoc()) {
    $chart_labels[] = $row['date'];
    $chart_data[] = $row['daily_income'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Dashboard</h1>
            </div>
            <!-- <div class="card-dashboard">
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
            </div> -->

            <div class="card-chart">
                <h2><i class="fa-solid fa-chart-line"></i> Pendapatan Harian (Last 30 Days)</h2>
                <div class="chart-container">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            <div class="card-chart">
                <h2>Stok Display (Rendah)</h2>
                <div class="table-stock">

                    <table>
                        <thead>
                            <tr>
                                <td>Id</td>
                                <td>Nama Produk</td>
                                <td>Stok</td>
                                <td>Expired Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM products WHERE Stock <= 10";

                            $result = $conn->query($query);
                            if ($result && $result->num_rows > 0) {
                                while ($d = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $d['Id'] ?></td>
                                        <td><?= htmlspecialchars($d['Name']) ?></td>
                                        <td><?= $d['Stock'] ?></td>
                                        <td><?= $d['Expired_Date'] ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-chart">
                <h2>Stok Gudang</h2>
                <div class="table-stock">

                    <table>
                        <thead>
                            <tr>
                                <td>Id</td>
                                <td>Nama Produk</td>
                                <td>Stok</td>
                                <td>Expired Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT warehouses.Id, products.Name, warehouses.Stock, warehouses.Expired_Date
                                    FROM warehouses
                                    INNER JOIN products ON warehouses.Product_Id = products.Id";

                            $result = $conn->query($query);
                            if ($result && $result->num_rows > 0) {
                                while ($d = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $d['Id'] ?></td>
                                        <td><?= htmlspecialchars($d['Name']) ?></td>
                                        <td><?= $d['Stock'] ?></td>
                                        <td><?= $d['Expired_Date'] ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            const incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chart_labels) ?>,
                    datasets: [{
                        label: 'Pendapatan harian',
                        data: <?= json_encode($chart_data) ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Income: ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>