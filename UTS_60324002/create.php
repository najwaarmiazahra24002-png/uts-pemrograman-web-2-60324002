<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';

    $errors = [];
    $kode_kategori = '';
    $nama_kategori = '';
    $deskripsi = '';
    $status = 'Aktif';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // TODO: Ambil dan sanitasi data dari form
        $kode_kategori = trim(htmlspecialchars($_POST['kode_kategori']));
        $nama_kategori = trim(htmlspecialchars($_POST['nama_kategori']));
        $deskripsi = trim(htmlspecialchars($_POST['deskripsi']));
        $status = $_POST['status'];

        // TODO: Validasi kode kategori
        if (empty($kode_kategori)) {
            $errors[] = "Kode kategori wajib diisi"; 
        } else {
            if (strlen($kode_kategori) < 4 || strlen($kode_kategori) > 10) {
                $errors[] = "Kode Kategori harus 4-10 karakter";
            }
            if (strpos($kode_kategori, 'KAT-') !==0) {
                $errors[] = "Kode Kategori harus diawali 'KAT-'";
            }
        }

        // TODO: Validasi nama kategori
        if (empty($nama_kategori)) {
            $errors[] = "Nama kategori wajib diisi"; 
        } else {
            if (strlen($nama_kategori) < 3) {
                $errors[] = "Nama minimal 3 karakter";
            }
            if (strlen($nama_kategori) > 50) {
                $errors[] = "Nama maksimal 50 karakter";
            }
        }

        // TODO: Validasi deskripsi
        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter"; 
        }
        // TODO: Validasi status
        if ($status !== 'Aktif' && $status !== 'Nonaktif') {
            $errors[] = "Status tidak valid"; 
        }
        // TODO: Cek duplikasi kode
        if (empty($errors)) {
            $cek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
            $cek->bind_param("s", $kode_kategori);
            $cek->execute();
            if ($cek->num_rows > 0) {
                $errors[] = "Kode kategori sudah digunakan";
            }
            $cek->close();
        }
        
        // TODO: Jika tidak ada error, insert data
        if (count($errors) == 0) {
            $stmt = $conn->prepare("INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)");  
            $stmt->bind_param("ssss", 
                $kode_kategori,
                $nama_kategori,
                $deskripsi,
                $status
            );
            
        // TODO: Redirect jika berhasil
        if ($stmt->execute()) {;
                header("Location: index.php?success=" . urlencode("Kategori '$nama_kategori' berhasil ditambahkan"));
                exit();
            } else {
                $errors[] = "Error database: " . $stmt->error;
            }
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Tambah Kategori Baru</h4>
                    </div>
                    <div class="card-body">
                        <!-- TODO: Tampilkan error jika ada -->
                        <?php if (count($errors) > 0): ?>
                        <div class="alert alert-danger">
                            <h6><i class="bi bi-exclamation-triangle"></i> Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <!-- Kode Kategori -->
                            <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kode_kategori" class="form-label">
                                    Kode Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kode_kategori" 
                                       name="kode_kategori" 
                                       value="<?php echo htmlspecialchars($kode_kategori); ?>"
                                       placeholder="KAT-001" 
                                       required>
                            </div>
                            <!-- Nama Kategori -->
                            <div class="col-md-8 mb-3">
                                <label for="nama_kategori" class="form-label">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_kategori" 
                                       name="nama_kategori" 
                                       value="<?php echo htmlspecialchars($nama_kategori); ?>"
                                       placeholder="Masukkan Nama kategori" 
                                       required>
                            </div>
                            <!-- Deskripsi -->
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" 
                                       id="deskripsi" 
                                       name="deskripsi" 
                                       placeholder="Deskripsi singkat tentang buku..."><?php echo htmlspecialchars($deskripsi); ?></textarea>
                            </div>
                            <!-- Status -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Status</label>

                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="status"
                                           value="Aktif"
                                           id="aktif"
                                           <?php echo ($status == 'Aktif') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="aktif">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="status"
                                           value="Nonaktif"
                                           id="nonaktif"
                                           <?php echo ($status == 'Nonaktif') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="nonaktif">Nonaktif</label>
                                </div>
                            </div> 
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="index.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>