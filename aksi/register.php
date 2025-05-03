<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $Name = trim($_POST['Name']);
    $Email = trim($_POST['Email']);
    $Role = trim($_POST['Role']);
    $Password = trim($_POST['Password']);

    if (empty($Name) || empty($Email) || empty($Role) || empty($Password)) {
        echo "Semua field wajib diisi.";
        exit();
    }

    $allowed_roles = ['Admin', 'Cashier'];
    if (!in_array($Role, $allowed_roles)) {
        echo "Peran tidak valid.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO Accounts (Name, Email, Role, Password) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $Name, $Email, $Role, $Password);
        if ($stmt->execute()) {
            echo "Register berhasil!";
            if ($Role === 'Admin') {
                header("Location: ../admin/index.php");
            } elseif ($Role === 'Cashier') {
                header("Location: ../kasir/kasir.php");
            } else {
                echo "Role tidak valid.";
            }
        } else {
            echo "Gagal register: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare statement gagal: " . $conn->error;
    }

    // $stmt = $conn->prepare("INSERT INTO employees (Name, Phone, Email, Password) VALUES (?, ?, ?, ?)");
    // if ($stmt) {
    //     $stmt->bind_param("ssss", $Name, $Phone, $Email, $PasswordHash);
    //     if ($stmt->execute()) {
    //         echo "Register berhasil!";
    //     } else {
    //         echo "Gagal register: " . $stmt->error;
    //     }
    //     $stmt->close(); 
    // } else {
    //     echo "Prepare statement gagal: " . $conn->error;
    // }
} else {
    echo "Harap isi form!";
}

$conn->close();
?>