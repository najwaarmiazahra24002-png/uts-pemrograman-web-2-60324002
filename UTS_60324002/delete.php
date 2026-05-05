<?php
require_once 'config/database.php';
 
// TODO: Validasi ID dari GET
if (!isset($_GET['id_kategori']) || empty($_GET['id_kategori'])) {
    header("Location: index.php?error=ID kategori tidak valid");
    exit();
}
$id_kategori = $_GET['id_kategori']; 

// TODO: Cek keberadaan data
$stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE id_kategori = ?");
$stmt->bind_param("i", $id_kategori);
$stmt->execute();
$result = $stmt->get_result();
 
if ($result->num_rows == 0) {
    $stmt->close();
    header("Location: index.php?error=Kategori tidak ditemukan");
    exit();
}
$kategori = $result->fetch_assoc();
$stmt->close();

// TODO: Delete data
$stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmt->bind_param("i", $id_kategori);
// TODO: Redirect dengan pesan
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        header("Location: index.php?success=" . urlencode("Kategori berhasil dihapus"));
        exit();
    } else {
        $stmt->close();
        closeConnection();
        header("Location: index.php?error=Gagal menghapus data");
        exit();
    }
} else {
    $error = $stmt->error;
    $stmt->close();
    closeConnection();
    header("Location: index.php?error=" . urlencode("Error database: $error"));
    exit();
}
?>