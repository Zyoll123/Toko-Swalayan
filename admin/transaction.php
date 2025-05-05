<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];

$query = "SELECT transactions.Id, transactions.Transaction_Date, transactions.Total, transactions.Money_Paid, transactions.Change, accounts.Name AS Cashier_Name, customers.Name AS Customers_Name
        FROM transactions
        JOIN accounts ON transactions.Employee_Id = accounts.Id
        JOIN customers ON transactions.Customer_Id = customers.Id";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/transaction.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="selection-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Id Transaction</th>
                            <th>Transaction Date</th>
                            <th>Total</th>
                            <th>Money Paid</th>
                            <th>Change</th>
                            <th>Cashier Name</th>
                            <th>Customer Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $result = $conn->query($query);
                        if ($result && $result->num_rows > 0) {
                            while ($d = $result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= htmlspecialchars($d['Id']) ?></td>
                                    <td><?= htmlspecialchars($d['Transaction_Date']) ?></td>
                                    <td><?= htmlspecialchars($d['Total']) ?></td>
                                    <td><?= htmlspecialchars($d['Money_Paid']) ?></td>
                                    <td><?= htmlspecialchars($d['Change']) ?></td>
                                    <td><?= htmlspecialchars($d['Cashier_Name']) ?></td>
                                    <td><?= htmlspecialchars($d['Customer_Name']) ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>