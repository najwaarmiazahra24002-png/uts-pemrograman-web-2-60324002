<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    // TODO: Query data kategori
    $query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    // TODO: Cek hasil query
    $result = $stmt->get_result();
    ?>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori Buku</h2>
            <a href="create.php" class="btn btn-primary">Tambah Kategori</a>
        </div>
        
        <?php
        // TODO: Tampilkan pesan sukses/error 
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show">';
            echo '<i class="bi bi-check-circle"></i> ' . htmlspecialchars($_GET['success']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
    
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show">';
            echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($_GET['error']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        ?>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="100">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // TODO: Loop data dan tampilkan
                        while($row = $result->fetch_assoc()) :
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><code><?php echo htmlspecialchars($row['kode_kategori']); ?></code></td>
                            <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                            <td class="text-center">
                                <?php if ($row['status'] == 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="edit.php?id_kategori=<?= $row['id_kategori']; ?>" 
                                    class="btn btn-warning btn-sm">Edit
                                </a>
                                <button onclick="confirmDelete(<?= $row['id_kategori']; ?>)"
                                    class="btn btn-danger btn-sm">Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function confirmDelete(id_kategori){
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            window.location.href = 'delete.php?id_kategori=' + id_kategori;
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>