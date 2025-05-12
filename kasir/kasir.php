<?php
session_start();
include '../aksi/koneksi.php';
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit;
}

$id_kasir = $_SESSION['Id'];
$kasirName = $_SESSION['Name'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$idSearch = isset($_GET['idSearch']) ? $_GET['idSearch'] : '';
// $customer = isset($_GET['customer']) ? $_GET['customer'] : '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $Id = $_POST['Id'];
    $Quantity = $_POST['Quantity'];
    $query_product = mysqli_query($conn, "SELECT * FROM products WHERE Id = '$Id'");
    $result_product = mysqli_fetch_assoc($query_product);

    if ($result_product['Stock'] >= $Quantity) {
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['Id'] == $Id) {
                $new_qty = $item['Quantity'] + $Quantity;
                if ($result_product['Stock'] >= $new_qty) {
                    $_SESSION['cart'][$key]['Quantity'] = $new_qty;
                    $_SESSION['cart'][$key]['Subtotal'] = $result_product['Price'] * $new_qty;
                    $found = true;
                } else {
                    echo "<script>alert('Stock tidak cukup!');</script>";
                    $found = true;
                }
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'Id' => $Id,
                'Name' => $result_product['Name'],
                'Price' => $result_product['Price'],
                'Quantity' => $Quantity,
                'Subtotal' => $result_product['Price'] * $Quantity
            ];
        }
    } else {
        echo "<script>alert('Stock tidak cukup!');</script>";
    }
}

if (isset($_POST['remove_item'])) {
    $index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

$invoice = null;
if (isset($_POST['checkout'])) {
    $total_uang = $_POST['total_uang'];
    $total_belanja = array_sum(array_column($_SESSION['cart'], 'Subtotal'));
    $tanggal = date("Y-m-d H:i:s");
    if ($total_uang >= $total_belanja) {
        $kembalian = $total_uang - $total_belanja;
        mysqli_query($conn, "INSERT INTO transactions (Transaction_Date, Total, Money_Paid, `Change`, Employee_Id)
        VALUES ('$tanggal', '$total_belanja', '$total_uang', '$kembalian', '$id_kasir')");
        $transaction_id = mysqli_insert_id($conn);
        foreach ($_SESSION['cart'] as $item) {
            mysqli_query($conn, "INSERT INTO transaction_details (Quantity, Subtotal, Product_Id, Transaction_Id)
            VALUES ('{$item['Quantity']}', '{$item['Subtotal']}', '{$item['Id']}', '$transaction_id')");

            $cek_produk = mysqli_query($conn, "SELECT * FROM products WHERE id = '{$item['Id']}'");
            $data = mysqli_fetch_assoc($cek_produk);
            $stok_baru = $data['Stock'] - $item['Quantity'];
            mysqli_query($conn, "UPDATE products SET Stock = '$stok_baru' WHERE Id = '{$item['Id']}'");
        }

        $invoice = [
            "kasir" => $kasirName,
            "tanggal" => $tanggal,
            "total_belanja" => $total_belanja,
            "total_uang" => $total_uang,
            "kembalian" => $kembalian,
            "items" => $_SESSION['cart']
        ];

        $_SESSION['cart'] = [];
    } else {
        echo "<script>alert('Uang yang diberikan tidak cukup!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir</title>
    <link rel="stylesheet" href="style/kasir.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <h2>Nama Toko</h2>
        </div>
        <div class="kasir-info">
            <span><?php echo htmlspecialchars($kasirName); ?></span>
            <a href="../aksi/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
    <div class="container">

        <div class="content">
            <div class="transaction-section">
                <form action="" method="post">
                    <div class="id-input">
                        <input type="number" name="Id" id="IdSearch" placeholder="Id"
                            value="<?= htmlspecialchars($idSearch) ?>">
                    </div>
                    <div class="search-input">
                        <input type="text" id="liveSearch" placeholder="Name" name="Name"
                            value="<?php echo htmlspecialchars($search) ?>" autocomplete="off" required>
                    </div>
                    <div class="quantity-input">
                        <input type="number" name="Quantity" id="Quantity" min="1" placeholder="Qty" required>
                    </div>
                    <div class="added-product-transaction">
                        <button type="submit" name="add_to_cart">ADD</button>
                    </div>
                </form>
            </div>

            <div class="menu-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>PRICE</th>
                            <th>STOCK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM products";
                        $where = [];

                        if (!empty($search)) {
                            $where[] = "Name LIKE '" . $conn->real_escape_string($search) . "%'";
                        }

                        if (!empty($idSearch)) {
                            $where[] = "Id = " . intval($idSearch);
                        }

                        if (!empty($where)) {
                            $query .= " WHERE " . implode(" AND ", $where);
                        }

                        $result_product = $conn->query($query);

                        if ($result_product && $result_product->num_rows > 0) {
                            while ($row = $result_product->fetch_assoc()) {
                                $format_price = number_format($row['Price'], 0, ',', '.');
                                ?>
                                <tr class="clickable-row" data-id="<?= $row['Id'] ?>"
                                    data-name="<?= htmlspecialchars($row['Name']) ?>" data-stock="<?= $row['Stock'] ?>">
                                    <td><?= $row['Id'] ?></td>
                                    <td><?= htmlspecialchars($row['Name']) ?></td>
                                    <td><?= $format_price ?></td>
                                    <td><?= $row['Stock'] ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'>No products found</td></tr>";
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="transaction-info">
            <div class="cart-header">
                <!-- <input type="text" placeholder="Customer"> -->
                <p id="liveClock">Loading time...</p>
            </div>
            <div class="cart-items">
                <?php if (empty($_SESSION['cart'])) { ?>
                    <div class="empty-cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <p>Keranjang belanja kosong</p>
                        <small>Tambah produk untuk memulai transaksi</small>
                    </div>
                <?php } else { ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item) { ?>
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <div class="cart-item-name"><?= $item['Name'] ?></div>
                                <div class="cart-item-price">Rp<?= number_format($item['Price'], 0, ",", ".") ?></div>
                            </div>
                            <div class="cart-item-qty">x<?= $item['Quantity'] ?></div>
                            <div class="cart-item-subtotal">Rp<?= number_format($item['Subtotal'], 0, ",", ".") ?></div>
                            <form action="" method="post" style="display: inline;">
                                <input type="hidden" name="item_index" value="<?= $index ?>">
                                <button type="submit" name="remove_item" class="cart-item-remove">
                                    <i class="fa-solid fa-xmark"></i> </button>
                            </form>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="cart-summary">
                <?php
                $total_keranjang = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $total_keranjang += $item['Subtotal'];
                }
                ?>
                <div class="cart-Total">
                    <span>Total:</span>
                    <span>Rp<?= number_format($total_keranjang, 0, ",", ".") ?></span>
                </div>
                <form action="" method="post">
                    <input type="number" name="total_uang" id="total_uang" placeholder="Masukkan jumlah uang"
                        class="payment-input" required>
                    <button type="submit" name="checkout" class="checkout-btn" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>
                        <i class="fa fa-check-circle"></i>Proses Pembayaran
                    </button>
                </form>
            </div>

        </div>
        <div class="modal" id="myModal" style="display: <?= $invoice ? 'block' : 'none' ?>;">
            <div class="modal-content">
                <div class="invoice">
                    <div class="invoice-header">
                        <div class="invoice-title">STRUK PEMBAYARAN</div>
                        <div class="invoice-subtitle">MUKTI ABADI - Point of Sale</div>
                    </div>

                    <div class="invoice-info">
                        <div>
                            <strong>Kasir:</strong> <?= $invoice['kasir'] ?>
                        </div>
                        <div>
                            <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($invoice['tanggal'])) ?>
                        </div>
                    </div>

                    <div class="invoice-details">
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
                    </div>

                    <div class="invoice-summary">
                        <div class="invoice-row">
                            <span>Total Belanja:</span>
                            <span>Rp<?= number_format($invoice['total_belanja'], 0, ",", ".") ?></span>
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

                    <div class="invoice-action">
                        <button onclick="window.print()" class="invoice-btn btn-print">
                            <i class="fa-solid fa-print"></i>Print
                        </button>
                        <button onclick="resetTransaction()" class="invoice-btn btn-transaction">
                            <i class="fas fa-redo"></i>Transaksi Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="js/kasir.js"></script>
    <script>
        function resetTransaction() {
            var modal = document.getElementById('myModal')
            if (modal) {
                modal.style.display = 'none'
            }
        }
    </script>
</body>

</html>