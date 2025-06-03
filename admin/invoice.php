    <?php
    include '../aksi/koneksi.php';

    if (!isset($_GET['Id'])) {
        echo "<p>Id Transaksi tidak ditemukan.</p>";
        exit;
    }

    $Id = (int)$_GET['Id'];

    $queryTransaksi = "
        SELECT 
            t.id AS transaction_id, 
            t.Transaction_Date, 
            t.Subtotal, 
            t.PPN, 
            t.Total, 
            t.Money_Paid, 
            t.`Change`,
            u.Name AS kasir
        FROM transactions t
        JOIN accounts u ON t.Employee_Id = u.id
        WHERE t.id = $Id
    ";

    $resultTransaksi = mysqli_query($conn, $queryTransaksi);
    $invoice = mysqli_fetch_assoc($resultTransaksi);

    if (!$invoice) {
        echo "<p>Transaksi tidak ditemukan.</p>";
        exit;
    }

    $queryDetail = "SELECT products.Name, transaction_details.Quantity, products.Price, transaction_details.Subtotal
                FROM transaction_details
                JOIN products ON transaction_details.Product_Id = products.Id
                WHERE Transaction_Id = $Id"

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>PrimeMarket - Invoice #<?= $Id ?></title>
        <link rel="stylesheet" href="style/invoice.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    </head>
    <body>
        <div class="invoice-container">
            <div class="invoice-header">
                <div class="invoice-title">STRUK PEMBAYARAN</div>
                <div class="invoice-subtitle">PRIME MARKET - Point of Sale</div>
            </div>

            <div class="invoice-info">
                <div><strong>Kasir:</strong> <?= htmlspecialchars($invoice['kasir']) ?></div>
                <div><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($invoice['Transaction_Date'])) ?></div>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query($queryDetail);
                    if ($result && $result->num_rows > 0) {
                                while ($d = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= $d['Name'] ?></td>
                                        <td><?= $d['Quantity'] ?></td>
                                        <td>Rp<?= number_format($d['Price'], 0, ",", ".") ?></td>
                                        <td>Rp<?= number_format($d['Subtotal'], 0, ",", ".") ?></td>
                                    </tr>
                                    <?php
                                    }
                                }
                    ?>
                </tbody>
            </table>

            <div class="invoice-summary">
                <div class="invoice-row">
                    <span>Total Belanja:</span>
                    <span>Rp<?= number_format($invoice['Subtotal'], 0, ",", ".") ?></span>
                </div>
                <div class="invoice-row">
                    <span>PPN (12%):</span>
                    <span>Rp<?= number_format($invoice['PPN'], 0, ",", ".") ?></span>
                </div>
                <div class="invoice-row">
                    <span>Total:</span>
                    <span>Rp<?= number_format($invoice['Total'], 0, ",", ".") ?></span>
                </div>
                <div class="invoice-row">
                    <span>Uang:</span>
                    <span>Rp<?= number_format($invoice['Money_Paid'], 0, ",", ".") ?></span>
                </div>
                <div class="invoice-row invoice-kembalian">
                    <span>Kembalian:</span>
                    <span>Rp<?= number_format($invoice['Change'], 0, ",", ".") ?></span>
                </div>
            </div>

            <div class="invoice-footer">
                <p>Terima kasih atas kunjungan anda!</p>
                <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
            </div>

            <div class="invoice-actions">
                <button onclick="window.print()" class="invoice-btn btn-print">
                    <i class="fa-solid fa-print"></i>Print
                </button>
                <button onclick="window.location.href='transaction.php'" class="invoice-btn btn-back">
                    <i class="fas fa-arrow-left"></i>Kembali ke Laporan
                </button>
            </div>
        </div>
    </body>
    </html>
