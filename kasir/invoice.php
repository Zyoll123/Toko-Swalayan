<?php
session_start();
include '../aksi/koneksi.php';
if (!isset($_SESSION['Name']) || !isset($_SESSION['invoice'])) {
    header("Location: ../login/login.html");
    exit;
}

$invoice = $_SESSION['invoice'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrimeMarket - Invoice #<?= $invoice['transaction_id'] ?></title>
    <link rel="stylesheet" href="style/invoice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="invoice-title">STRUK PEMBAYARAN</div>
            <div class="invoice-subtitle">PRIME MARKET - Point of Sale</div>
        </div>

        <div class="invoice-info">
            <div>
                <strong>Kasir:</strong> <?= $invoice['kasir'] ?>
            </div>
            <div>
                <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($invoice['tanggal'])) ?>
            </div>
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
                <?php foreach ($invoice['items'] as $item) { ?>
                    <tr>
                        <td><?= $item['Name'] ?></td>
                        <td><?= $item['Quantity'] ?></td>
                        <td>Rp<?= number_format($item['Price'], 0, ",", ".") ?></td>
                        <td>Rp<?= number_format($item['Subtotal'], 0, ",", ".") ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="invoice-row">
                <span>Total Belanja:</span>
                <span>Rp<?= number_format($invoice['total_belanja'], 0, ",", ".") ?></span>
            </div>
            <div class="invoice-row">
                <span>PPN (12%):</span>
                <span>Rp<?= number_format($invoice['total_belanja'] * 0.12, 0, ",", ".") ?></span>
            </div>
            <div class="invoice-row">
                <span>Total:</span>
                <span>Rp<?= number_format($invoice['total_belanja'] * 1.12, 0, ",", ".") ?></span>
            </div>
            <div class="invoice-row">
                <span>Uang:</span>
                <span>Rp<?= number_format($invoice['total_uang'], 0, ",", ".") ?></span>
            </div>
            <div class="invoice-row invoice-kembalian">
                <span>Kembalian:</span>
                <span>Rp<?= number_format($invoice['kembalian'], 0, ",", ".") ?></span>
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
            <button onclick="window.location.href='kasir.php'" class="invoice-btn btn-back">
                <i class="fas fa-arrow-left"></i>Kembali ke Kasir
            </button>
        </div>
    </div>
</body>
</html>
<?php
// Clear the invoice from session after displaying
unset($_SESSION['invoice']);
?>