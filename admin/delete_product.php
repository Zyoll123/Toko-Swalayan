<?php
include '../aksi/koneksi.php';

if (isset($_GET['Id'])) {
    $Id = $_GET['Id'];

    $stmt = $conn->prepare("UPDATE transaction_details SET Product_Id = NULL WHERE Product_Id = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM warehouses WHERE Product_Id = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();

    mysqli_query($conn, "DELETE FROM products WHERE Id = $Id");
    header("Location: products.php");
} else {
    echo "Id tidak ditemukan.";
}

$conn->close();
?>