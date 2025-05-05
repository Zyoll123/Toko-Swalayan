<?php
include '../aksi/koneksi.php';

if (isset($_GET['Id'])) {
    $Id = $_GET['Id'];

    mysqli_query($conn, "DELETE FROM accounts WHERE Id = $Id");
    header("Location: users.php");
} else {
    echo "Id tidak ditemukan.";
}

$conn->close();
?>