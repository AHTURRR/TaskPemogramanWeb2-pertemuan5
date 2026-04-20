<?php
// Initialize variables
$nama = $email = $telepon = $alamat = $jenis_kelamin = $tanggal_lahir = $pekerjaan = '';
$errors = [];
$is_submitted = false;
$is_valid = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_submitted = true;
    
    // Get and sanitize input
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telepon = isset($_POST['telepon']) ? trim($_POST['telepon']) : '';
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
    $tanggal_lahir = isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '';
    $pekerjaan = isset($_POST['pekerjaan']) ? $_POST['pekerjaan'] : '';
    
    // Validation
    
    // Validasi Nama Lengkap
    if (empty($nama)) {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama lengkap minimal 3 karakter.';
    }
    
    // Validasi Email
    if (empty($email)) {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email tidak valid.';
    }
    
    // Validasi Telepon
    if (empty($telepon)) {
        $errors['telepon'] = 'Telepon wajib diisi.';
    } elseif (!preg_match('/^08[0-9]{8,11}$/', $telepon)) {
        $errors['telepon'] = 'Telepon harus diawali 08 dan total panjang 10-13 digit.';
    }
    
    // Validasi Alamat
    if (empty($alamat)) {
        $errors['alamat'] = 'Alamat wajib diisi.';
    } elseif (strlen($alamat) < 10) {
        $errors['alamat'] = 'Alamat minimal 10 karakter.';
    }
    
    // Validasi Jenis Kelamin
    if (empty($jenis_kelamin)) {
        $errors['jenis_kelamin'] = 'Jenis kelamin wajib dipilih.';
    }
    
    // Validasi Tanggal Lahir
    if (empty($tanggal_lahir)) {
        $errors['tanggal_lahir'] = 'Tanggal lahir wajib diisi.';
    } else {
        // Calculate age using DateTime
        try {
            $birth_date = new DateTime($tanggal_lahir);
            $today = new DateTime();
            $age = $today->diff($birth_date)->y;
            
            if ($age < 10) {
                $errors['tanggal_lahir'] = 'Umur minimal 10 tahun.';
            }
        } catch (Exception $e) {
            $errors['tanggal_lahir'] = 'Format tanggal tidak valid.';
        }
    }
    
    // Validasi Pekerjaan
    if (empty($pekerjaan)) {
        $errors['pekerjaan'] = 'Pekerjaan wajib dipilih.';
    }
    
    // If no errors, form is valid
    if (empty($errors)) {
        $is_valid = true;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Anggota Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-bottom: none;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .card-body {
            padding: 30px;
            background-color: #ffffff;
        }
        
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
            font-size: 0.95rem;
        }
        
        .form-control, 
        .form-select {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 0.65rem 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, 
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-control.is-invalid, 
        .form-select.is-invalid {
            border-color: #dc3545;
            background-image: none;
        }
        
        .form-control.is-invalid:focus, 
        .form-select.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .invalid-feedback {
            display: block;
            margin-top: 6px;
            font-size: 0.875rem;
            color: #dc3545;
            font-weight: 500;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 6px;
            font-weight: 600;
            padding: 11px 30px;
            font-size: 0.95rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8a 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .success-card .card-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .data-item {
            display: flex;
            padding: 14px 0;
            border-bottom: 1px solid #ecf0f1;
            align-items: flex-start;
            gap: 20px;
        }
        
        .data-item:last-child {
            border-bottom: none;
        }
        
        .data-label {
            font-weight: 600;
            color: #667eea;
            min-width: 140px;
            font-size: 0.95rem;
        }
        
        .data-value {
            color: #555;
            word-wrap: break-word;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .radio-group {
            display: flex;
            gap: 25px;
            margin-top: 10px;
        }
        
        .form-check {
            padding-left: 0;
        }
        
        .form-check-input {
            margin-top: 4px;
            margin-right: 8px;
            width: 18px;
            height: 18px;
            cursor: pointer;
            border: 1px solid #dee2e6;
            background-color: #fff;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-label {
            cursor: pointer;
            user-select: none;
            font-size: 0.95rem;
            color: #333;
        }
        
        .text-danger {
            font-size: 1rem;
            font-weight: 700;
        }
        
        .success-box {
            background-color: #f0f8f5;
            border-left: 4px solid #38ef7d;
            padding: 16px;
            margin-top: 20px;
            border-radius: 6px;
        }
        
        .success-box p {
            margin: 0;
            font-size: 0.95rem;
        }
        
        .success-box p:first-child {
            color: #11998e;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .success-box p:last-child {
            color: #666;
            font-size: 0.9rem;
        }
        
        .btn-outline-primary {
            color: #667eea;
            border-color: #667eea;
            border-radius: 6px;
            font-weight: 600;
        }
        
        .btn-outline-primary:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
        }
        
        @media (max-width: 576px) {
            .card-header h2 {
                font-size: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .data-item {
                flex-direction: column;
                gap: 4px;
            }
            
            .data-label {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Form Card (shown if not valid) -->
        <?php if (!$is_valid): ?>
        <div class="card">
            <div class="card-header">
                <h2>📝 Formulir Registrasi Anggota Perpustakaan</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <!-- Nama Lengkap -->
                    <div class="form-group mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?php echo isset($errors['nama']) ? 'is-invalid' : ''; ?>" 
                            id="nama" 
                            name="nama" 
                            placeholder="Contoh: Ahmad Turmudi" 
                            value="<?php echo htmlspecialchars($nama); ?>"
                        >
                        <?php if (isset($errors['nama'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['nama']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input 
                            type="email" 
                            class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                            id="email" 
                            name="email" 
                            placeholder="Contoh: nama@email.com" 
                            value="<?php echo htmlspecialchars($email); ?>"
                        >
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Telepon -->
                    <div class="form-group mb-3">
                        <label for="telepon" class="form-label">Telepon <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control <?php echo isset($errors['telepon']) ? 'is-invalid' : ''; ?>" 
                            id="telepon" 
                            name="telepon" 
                            placeholder="Contoh: 081234567890" 
                            value="<?php echo htmlspecialchars($telepon); ?>"
                        >
                        <?php if (isset($errors['telepon'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['telepon']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Alamat -->
                    <div class="form-group mb-3">
                        <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control <?php echo isset($errors['alamat']) ? 'is-invalid' : ''; ?>" 
                            id="alamat" 
                            name="alamat" 
                            rows="4" 
                            placeholder="Masukkan alamat lengkap (minimal 10 karakter)"
                        ><?php echo htmlspecialchars($alamat); ?></textarea>
                        <?php if (isset($errors['alamat'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['alamat']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div class="form-group mb-3">
                        <label class="form-label d-block">Jenis Kelamin <span class="text-danger">*</span></label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="jenis_kelamin" 
                                    id="laki" 
                                    value="Laki-laki"
                                    <?php echo $jenis_kelamin === 'Laki-laki' ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="laki">
                                    Laki-laki
                                </label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="jenis_kelamin" 
                                    id="perempuan" 
                                    value="Perempuan"
                                    <?php echo $jenis_kelamin === 'Perempuan' ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="perempuan">
                                    Perempuan
                                </label>
                            </div>
                        </div>
                        <?php if (isset($errors['jenis_kelamin'])): ?>
                            <div class="invalid-feedback d-block">
                                <?php echo htmlspecialchars($errors['jenis_kelamin']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tanggal Lahir -->
                    <div class="form-group mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input 
                            type="date" 
                            class="form-control <?php echo isset($errors['tanggal_lahir']) ? 'is-invalid' : ''; ?>"}" 
                            id="tanggal_lahir" 
                            name="tanggal_lahir" 
                            value="<?php echo htmlspecialchars($tanggal_lahir); ?>"
                        >
                        <?php if (isset($errors['tanggal_lahir'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['tanggal_lahir']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pekerjaan -->
                    <div class="form-group mb-4">
                        <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                        <select 
                            class="form-select <?php echo isset($errors['pekerjaan']) ? 'is-invalid' : ''; ?>" 
                            id="pekerjaan" 
                            name="pekerjaan"
                        >
                            <option value="" selected>-- Pilih Pekerjaan --</option>
                            <option value="Pelajar" <?php echo $pekerjaan === 'Pelajar' ? 'selected' : ''; ?>>Pelajar</option>
                            <option value="Mahasiswa" <?php echo $pekerjaan === 'Mahasiswa' ? 'selected' : ''; ?>>Mahasiswa</option>
                            <option value="Pegawai" <?php echo $pekerjaan === 'Pegawai' ? 'selected' : ''; ?>>Pegawai</option>
                            <option value="Lainnya" <?php echo $pekerjaan === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                        <?php if (isset($errors['pekerjaan'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['pekerjaan']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-submit">✓ Daftar</button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <!-- Success Card -->
        <div class="card success-card">
            <div class="card-header">
                <h2>✓ Registrasi Berhasil</h2>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4" style="font-size: 0.95rem;">Berikut adalah ringkasan data yang Anda daftarkan:</p>
                
                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <div class="data-item">
                        <span class="data-label">Nama Lengkap:</span>
                        <span class="data-value"><?php echo htmlspecialchars($nama); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Email:</span>
                        <span class="data-value"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Telepon:</span>
                        <span class="data-value"><?php echo htmlspecialchars($telepon); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Alamat:</span>
                        <span class="data-value"><?php echo htmlspecialchars($alamat); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Jenis Kelamin:</span>
                        <span class="data-value"><?php echo htmlspecialchars($jenis_kelamin); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Tanggal Lahir:</span>
                        <span class="data-value"><?php echo htmlspecialchars($tanggal_lahir); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Pekerjaan:</span>
                        <span class="data-value"><?php echo htmlspecialchars($pekerjaan); ?></span>
                    </div>
                </div>

                <div class="success-box">
                    <p>✓ Data Anda telah berhasil disimpan!</p>
                    <p>Terima kasih telah mendaftar sebagai anggota perpustakaan kami. Silakan log in untuk mengakses layanan perpustakaan.</p>
                </div>

                <!-- Register New Button -->
                <div class="d-grid gap-2 mt-4">
                    <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-primary">+ Daftar Anggota Baru</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
