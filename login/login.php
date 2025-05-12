<?php

use LDAP\Result;
session_start();
include '../aksi/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Name']) && isset($_POST['Password'])) {
        $Name = trim($_POST['Name']);
        $Password = trim($_POST['Password']);

        $stmt = $conn->prepare("SELECT * FROM Accounts WHERE Name = ? AND Password = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $Name, $Password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                $_SESSION['accounts'] = $user['accounts'];
                $_SESSION['Id'] = $user['Id'];
                $_SESSION['Name'] = $user['Name'];
                $_SESSION['Role'] = $user['Role'];

                if ($user['Role'] === 'Admin') {
                    header("Location: ../admin/index.php");
                } elseif ($user['Role'] === 'Cashier') {
                    header("Location: ../kasir/kasir.php");
                } else {
                    echo "Role tidak ditemukan";
                }

                exit();
            } else {
                echo "Name atau Password salah";
            }
    
            $stmt->close();
        } else {
            echo "Terjadi kesalahan saat menyiapkan statement.";
        }
    } else {
        echo "Name dan Password wajib diisi";
    }
}

$conn->close();

?>