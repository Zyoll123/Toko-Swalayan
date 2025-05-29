<?php
include '../aksi/koneksi.php';

if (isset($_GET['Id'])) {
    $Id = $_GET['Id'];

    $check_stmt = $conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE Category_Id = ?");
    $check_stmt->bind_param("i", $Id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['product_count'] > 0) {
        header("Location: products.php?error=Kategori tidak dapat dihapus karena masih digunakan oleh produk");
        exit();
    } else {
        $delete_stmt = $conn->prepare("DELETE FROM categories WHERE Id = ?");
        $delete_stmt->bind_param("i", $Id);
        
        if ($delete_stmt->execute()) {
            header("Location: products.php?success=Kategori berhasil dihapus");
        } else {
            header("Location: products.php?error=Gagal menghapus kategori");
        }
        exit();
    }
} else {
    echo "Id tidak ditemukan.";
}

$conn->close();
?>