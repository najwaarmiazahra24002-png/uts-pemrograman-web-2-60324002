<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';

    // TODO: Ambil ID dari GET
    if (!isset($_GET['id_kategori']) || empty($_GET['id_kategori'])) {
        header("Location: index.php?error=ID kategori tidak valid");
        exit();
    }

    $id_kategori = (int)$_GET['id_kategori'];
    $errors = [];

    // TODO: Retrieve data berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param("i", $id_kategori);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        header("Location: index.php?error=Kategori tidak ditemukan");
        exit();
    }
    
    $kategori = $result->fetch_assoc();
    $kode_kategori = $kategori['kode_kategori'];
    $nama_kategori= $kategori['nama_kategori'];
    $deskripsi= $kategori['deskripsi'];
    $status = $kategori['status'];
    $stmt->close();

    // TODO: Jika POST, proses update
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $kode_kategori = trim(htmlspecialchars($_POST['kode_kategori']));
        $nama_kategori = trim(htmlspecialchars($_POST['nama_kategori']));
        $deskripsi = trim(htmlspecialchars($_POST['deskripsi']));
        $status = $_POST['status'];
        // validasi 
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

        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter"; 
        }
        
        if ($status !== 'Aktif' && $status !== 'Nonaktif') {
            $errors[] = "Status tidak valid"; 
        }
        // cek duplikasi kode
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
            $stmt->bind_param("si", $kode_kategori, $id_kategori);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Kode kategori sudah digunakan";
            }
            $stmt->close();
        }
        // update database
        if (count($errors) == 0) {
            $stmt = $conn->prepare("UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?");
            $stmt->bind_param("ssssi", 
                $kode_kategori,
                $nama_kategori,
                $deskripsi,
                $status,
                $id_kategori
            );
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: index.php?success=" . urlencode("kategori berhasil diupdate"));
                exit();
            } else {
                $errors[] = "Error database: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Kategori</h4>
                    </div>
                    <div class="card-body">
                        <!-- error -->
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
                                       required>
                            </div>
                            <!-- Deskripsi -->
                            <div class="col-md-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" 
                                       id="deskripsi" 
                                       name="deskripsi" 
                                       rows="3"><?php echo htmlspecialchars($deskripsi); ?></textarea>
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