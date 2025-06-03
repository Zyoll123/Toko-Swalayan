<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];

$query = "SELECT transactions.Id, transactions.Transaction_Date, transactions.Total, transactions.Money_Paid, transactions.Change, accounts.Name AS Cashier_Name
        FROM transactions
        JOIN accounts ON transactions.Employee_Id = accounts.Id";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPORAN</title>
    <link rel="stylesheet" href="style/transaction.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>Laporan</h1>
            </div>
            <div class="selection-table">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Id Transaksi</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total</th>
                            <th>Uang Dibayar</th>
                            <th>Kembalian</th>
                            <th>Nama Kasir</th>
                            <th>Aksi</th>
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
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($d['Id']) ?></td>
                                    <td><?= htmlspecialchars($d['Transaction_Date']) ?></td>
                                    <td>Rp<?= number_format($d['Total'], 2) ?></td>
                                    <td>Rp<?= number_format($d['Money_Paid'], 2) ?></td>
                                    <td>Rp<?= number_format($d['Change'], 2) ?></td>
                                    <td><?= htmlspecialchars($d['Cashier_Name']) ?></td>
                                    <td class="action-buttons">
                                        <a href="invoice.php?Id=<?= $d['Id'] ?>" class="btn edit">Selengkapnya</a>
                                    </td>
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