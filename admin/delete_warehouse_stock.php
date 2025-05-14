<?php
include '../aksi/koneksi.php';

if (isset($_GET['Id'])) {
    $Id = $_GET['Id'];

    mysqli_query($conn, "DELETE FROM warehouses WHERE Id = $Id");
    header("Location: products.php");
} else {
    echo "Id tidak ditemukan.";
}

$conn->close();
?>