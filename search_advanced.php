<?php
session_start();

// ============================================
// 1. DATA & KONFIGURASI DASAR
// ============================================

// Data Buku List (12+ buku)
$buku_list = [
    [
        'kode' => 'BK001',
        'judul' => 'The Art of Computer Programming',
        'kategori' => 'Teknik',
        'pengarang' => 'Donald Knuth',
        'penerbit' => 'Addison-Wesley',
        'tahun' => 1968,
        'harga' => 450000,
        'stok' => 5
    ],
    [
        'kode' => 'BK002',
        'judul' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
        'kategori' => 'Teknik',
        'pengarang' => 'Robert C. Martin',
        'penerbit' => 'Prentice Hall',
        'tahun' => 2008,
        'harga' => 325000,
        'stok' => 8
    ],
    [
        'kode' => 'BK003',
        'judul' => 'To Kill a Mockingbird',
        'kategori' => 'Fiksi',
        'pengarang' => 'Harper Lee',
        'penerbit' => 'J.B. Lippincott',
        'tahun' => 1960,
        'harga' => 185000,
        'stok' => 12
    ],
    [
        'kode' => 'BK004',
        'judul' => 'Information Architecture: For The Web and Beyond',
        'kategori' => 'Teknik',
        'pengarang' => 'Louis Rosenfeld',
        'penerbit' => 'O\'Reilly Media',
        'tahun' => 2015,
        'harga' => 275000,
        'stok' => 4
    ],
    [
        'kode' => 'BK005',
        'judul' => 'The Great Gatsby',
        'kategori' => 'Fiksi',
        'pengarang' => 'F. Scott Fitzgerald',
        'penerbit' => 'Scribner',
        'tahun' => 1925,
        'harga' => 165000,
        'stok' => 0
    ],
    [
        'kode' => 'BK006',
        'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
        'kategori' => 'Teknik',
        'pengarang' => 'Gang of Four',
        'penerbit' => 'Addison-Wesley',
        'tahun' => 1994,
        'harga' => 395000,
        'stok' => 3
    ],
    [
        'kode' => 'BK007',
        'judul' => '1984',
        'kategori' => 'Fiksi',
        'pengarang' => 'George Orwell',
        'penerbit' => 'Penguin Books',
        'tahun' => 1949,
        'harga' => 145000,
        'stok' => 7
    ],
    [
        'kode' => 'BK008',
        'judul' => 'Introduction to Algorithms',
        'kategori' => 'Teknik',
        'pengarang' => 'Thomas Cormen',
        'penerbit' => 'MIT Press',
        'tahun' => 1990,
        'harga' => 520000,
        'stok' => 2
    ],
    [
        'kode' => 'BK009',
        'judul' => 'Sapiens: A Brief History of Humankind',
        'kategori' => 'Non-Fiksi',
        'pengarang' => 'Yuval Noah Harari',
        'penerbit' => 'Harvill Secker',
        'tahun' => 2011,
        'harga' => 289000,
        'stok' => 9
    ],
    [
        'kode' => 'BK010',
        'judul' => 'Javascript: The Good Parts',
        'kategori' => 'Teknik',
        'pengarang' => 'Douglas Crockford',
        'penerbit' => 'O\'Reilly Media',
        'tahun' => 2008,
        'harga' => 198000,
        'stok' => 6
    ],
    [
        'kode' => 'BK011',
        'judul' => 'Thinking, Fast and Slow',
        'kategori' => 'Non-Fiksi',
        'pengarang' => 'Daniel Kahneman',
        'penerbit' => 'Farrar, Straus and Giroux',
        'tahun' => 2011,
        'harga' => 215000,
        'stok' => 5
    ],
    [
        'kode' => 'BK012',
        'judul' => 'The Pragmatic Programmer: Your Journey to Mastery',
        'kategori' => 'Teknik', 
        'pengarang' => 'David Thomas',
        'penerbit' => 'Addison-Wesley',
        'tahun' => 1999,
        'harga' => 285000,
        'stok' => 4
    ],
];

// Get kategori unik
$kategori_list = array_unique(array_column($buku_list, 'kategori'));
sort($kategori_list);


// ============================================
// 2. AMBIL PARAMETER GET & INISIALISASI
// ============================================

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$min_harga = isset($_GET['min_harga']) ? $_GET['min_harga'] : '';
$max_harga = isset($_GET['max_harga']) ? $_GET['max_harga'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'semua';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'judul';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$export_csv = isset($_GET['export']) && $_GET['export'] === 'csv';

$errors = [];
$hasil = [];

// ============================================
// 3. VALIDASI INPUT
// ============================================

// Validasi harga
if (!empty($min_harga) && !empty($max_harga)) {
    $min_harga_num = (float)$min_harga;
    $max_harga_num = (float)$max_harga;
    
    if ($min_harga_num > $max_harga_num) {
        $errors[] = "⚠️ Harga minimum tidak boleh lebih besar dari harga maksimum";
    }
}

// Validasi tahun
$tahun_sekarang = (int)date('Y');
if (!empty($tahun)) {
    $tahun_input = (int)$tahun;
    if ($tahun_input < 1900 || $tahun_input > $tahun_sekarang) {
        $errors[] = "⚠️ Tahun harus berada antara 1900 dan " . $tahun_sekarang;
    }
}

// ============================================
// 4. FILTER DATA BUKU
// ============================================

if (empty($errors)) {
    $hasil = $buku_list;
    
    // Filter keyword
    if (!empty($keyword)) {
        $hasil = array_filter($hasil, function($buku) use ($keyword) {
            return stripos($buku['judul'], $keyword) !== false || 
                   stripos($buku['pengarang'], $keyword) !== false;
        });
        
        // Simpan ke recent searches
        if (!isset($_SESSION['recent'])) {
            $_SESSION['recent'] = [];
        }
        
        // Hapus duplikat dan tambah di awal
        $_SESSION['recent'] = array_diff($_SESSION['recent'], [$keyword]);
        array_unshift($_SESSION['recent'], $keyword);
        
        // Batasi max 5 recent searches
        $_SESSION['recent'] = array_slice($_SESSION['recent'], 0, 5);
    }
    
    // Filter kategori
    if (!empty($kategori)) {
        $hasil = array_filter($hasil, function($buku) use ($kategori) {
            return $buku['kategori'] === $kategori;
        });
    }
    
    // Filter harga
    if (!empty($min_harga) && !empty($max_harga)) {
        $min_harga_num = (float)$min_harga;
        $max_harga_num = (float)$max_harga;
        $hasil = array_filter($hasil, function($buku) use ($min_harga_num, $max_harga_num) {
            return $buku['harga'] >= $min_harga_num && $buku['harga'] <= $max_harga_num;
        });
    }
    
    // Filter tahun
    if (!empty($tahun)) {
        $tahun_input = (int)$tahun;
        $hasil = array_filter($hasil, function($buku) use ($tahun_input) {
            return $buku['tahun'] == $tahun_input;
        });
    }
    
    // Filter status ketersediaan
    if ($status === 'tersedia') {
        $hasil = array_filter($hasil, function($buku) {
            return $buku['stok'] > 0;
        });
    } elseif ($status === 'habis') {
        $hasil = array_filter($hasil, function($buku) {
            return $buku['stok'] == 0;
        });
    }
    
    // ============================================
    // 5. SORTING
    // ============================================
    
    $hasil = array_values($hasil); // Re-index array
    
    if ($sort === 'judul') {
        usort($hasil, function($a, $b) {
            return strcasecmp($a['judul'], $b['judul']);
        });
    } elseif ($sort === 'harga') {
        usort($hasil, function($a, $b) {
            return $a['harga'] - $b['harga'];
        });
    } elseif ($sort === 'tahun') {
        usort($hasil, function($a, $b) {
            return $b['tahun'] - $a['tahun'];
        });
    }
}

// ============================================
// 6. EXPORT CSV
// ============================================

if ($export_csv && empty($errors)) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="hasil_pencarian_buku.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Header CSV
    fputcsv($output, ['Kode', 'Judul', 'Kategori', 'Pengarang', 'Penerbit', 'Tahun', 'Harga', 'Stok']);
    
    // Data
    foreach ($hasil as $buku) {
        fputcsv($output, [
            $buku['kode'],
            $buku['judul'],
            $buku['kategori'],
            $buku['pengarang'],
            $buku['penerbit'],
            $buku['tahun'],
            'Rp ' . number_format($buku['harga'], 0, ',', '.'),
            $buku['stok'] > 0 ? $buku['stok'] . ' tersedia' : 'Habis'
        ]);
    }
    
    fclose($output);
    exit;
}

// ============================================
// 7. PAGINATION
// ============================================

$items_per_page = 10;
$total_items = count($hasil);
$total_pages = ceil($total_items / $items_per_page);

// Validasi page
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$start_index = ($page - 1) * $items_per_page;
$hasil_halaman = array_slice($hasil, $start_index, $items_per_page);

// ============================================
// 8. FUNCTION HIGHLIGHT KEYWORD
// ============================================

function highlightKeyword($text, $keyword) {
    if (empty($keyword)) return htmlspecialchars($text);
    return preg_replace(
        '/(' . preg_quote($keyword, '/') . ')/i',
        '<mark>$1</mark>',
        htmlspecialchars($text)
    );
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Buku Lanjutan - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .header-search {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header-search h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .header-search p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            border: none;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 0.65rem 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.65rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-search:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8a 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-export {
            background-color: #28a745;
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.65rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-export:hover {
            background-color: #218838;
            color: white;
        }
        
        .alert-custom {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }
        
        mark {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        table {
            font-size: 0.95rem;
        }
        
        table thead {
            background-color: #f8f9fa;
        }
        
        table th {
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #dee2e6;
        }
        
        table td {
            vertical-align: middle;
            padding: 12px;
        }
        
        .badge-stok {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-tersedia {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-habis {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .pagination {
            margin-top: 30px;
            justify-content: center;
        }
        
        .pagination .page-link {
            color: #667eea;
            border-color: #dee2e6;
            border-radius: 6px;
            margin: 0 4px;
        }
        
        .pagination .page-link:hover {
            color: #fff;
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .recent-searches {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .recent-searches h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .recent-searches a {
            display: inline-block;
            background-color: #e7f3ff;
            color: #667eea;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            margin-right: 8px;
            margin-bottom: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .recent-searches a:hover {
            background-color: #667eea;
            color: white;
        }
        
        .results-info {
            background-color: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .radio-group {
            display: flex;
            gap: 25px;
            margin-top: 10px;
        }
        
        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin-top: 3px;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .form-check-label {
            cursor: pointer;
            user-select: none;
            font-weight: 400;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .header-search h1 {
                font-size: 1.8rem;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 12px;
            }
            
            table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-main">
        <!-- Header -->
        <div class="header-search">
            <h1>🔍 Pencarian Buku Lanjutan</h1>
            <p>Temukan buku impian Anda dengan filter yang komprehensif</p>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-custom" role="alert">
                <?php foreach ($errors as $error): ?>
                    <div><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Search Form Card -->
        <div class="card">
            <div class="card-header">
                <h5>⚙️ Filter & Pencarian</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row">
                        <!-- Keyword -->
                        <div class="col-md-6 mb-3">
                            <label for="keyword" class="form-label">Kata Kunci</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="keyword" 
                                name="keyword" 
                                placeholder="Cari judul atau pengarang..." 
                                value="<?php echo htmlspecialchars($keyword); ?>"
                            >
                        </div>

                        <!-- Kategori -->
                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">-- Semua Kategori --</option>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <option value="<?php echo htmlspecialchars($kat); ?>" 
                                        <?php echo $kategori === $kat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Min Harga -->
                        <div class="col-md-6 mb-3">
                            <label for="min_harga" class="form-label">Harga Minimum (Rp)</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="min_harga" 
                                name="min_harga" 
                                placeholder="0" 
                                min="0" 
                                value="<?php echo htmlspecialchars($min_harga); ?>"
                            >
                        </div>

                        <!-- Max Harga -->
                        <div class="col-md-6 mb-3">
                            <label for="max_harga" class="form-label">Harga Maksimum (Rp)</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="max_harga" 
                                name="max_harga" 
                                placeholder="999999999" 
                                min="0" 
                                value="<?php echo htmlspecialchars($max_harga); ?>"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tahun -->
                        <div class="col-md-6 mb-3">
                            <label for="tahun" class="form-label">Tahun Terbit</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="tahun" 
                                name="tahun" 
                                placeholder="1900-<?php echo $tahun_sekarang; ?>" 
                                min="1900" 
                                max="<?php echo $tahun_sekarang; ?>" 
                                value="<?php echo htmlspecialchars($tahun); ?>"
                            >
                        </div>

                        <!-- Sorting -->
                        <div class="col-md-6 mb-3">
                            <label for="sort" class="form-label">Urutkan Berdasarkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="judul" <?php echo $sort === 'judul' ? 'selected' : ''; ?>>Judul (A-Z)</option>
                                <option value="harga" <?php echo $sort === 'harga' ? 'selected' : ''; ?>>Harga (Termurah-Termahal)</option>
                                <option value="tahun" <?php echo $sort === 'tahun' ? 'selected' : ''; ?>>Tahun (Terbaru-Terlama)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status Ketersediaan -->
                    <div class="mb-3">
                        <label class="form-label d-block">Status Ketersediaan</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="status" 
                                    id="status_semua" 
                                    value="semua" 
                                    <?php echo $status === 'semua' ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="status_semua">Semua</label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="status" 
                                    id="status_tersedia" 
                                    value="tersedia" 
                                    <?php echo $status === 'tersedia' ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="status_tersedia">Tersedia</label>
                            </div>
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="radio" 
                                    name="status" 
                                    id="status_habis" 
                                    value="habis" 
                                    <?php echo $status === 'habis' ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="status_habis">Habis</label>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-search">🔍 Cari Buku</button>
                        <a href="?" class="btn btn-outline-secondary">↻ Reset</a>
                        <?php if (!empty($hasil) && empty($errors)): ?>
                            <a href="?keyword=<?php echo htmlspecialchars(urlencode($keyword)); ?>&kategori=<?php echo htmlspecialchars(urlencode($kategori)); ?>&min_harga=<?php echo htmlspecialchars(urlencode($min_harga)); ?>&max_harga=<?php echo htmlspecialchars(urlencode($max_harga)); ?>&tahun=<?php echo htmlspecialchars(urlencode($tahun)); ?>&status=<?php echo htmlspecialchars(urlencode($status)); ?>&sort=<?php echo htmlspecialchars(urlencode($sort)); ?>&export=csv" class="btn btn-export">📥 Export CSV</a>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Recent Searches -->
                <?php if (!empty($_SESSION['recent'])): ?>
                <div class="recent-searches">
                    <h6>📝 Pencarian Terakhir Anda</h6>
                    <?php foreach ($_SESSION['recent'] as $recent): ?>
                        <a href="?keyword=<?php echo htmlspecialchars(urlencode($recent)); ?>">
                            <?php echo htmlspecialchars($recent); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Results -->
        <?php if (!empty($hasil) || isset($_GET['keyword']) || isset($_GET['kategori']) || isset($_GET['min_harga']) || isset($_GET['max_harga']) || isset($_GET['tahun'])): ?>
            <div class="card">
                <div class="card-header">
                    <h5>📚 Hasil Pencarian</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($errors)): ?>
                        <div class="results-info">
                            Ditemukan <strong><?php echo $total_items; ?></strong> buku dari total <strong><?php echo count($buku_list); ?></strong> buku
                        </div>

                        <?php if (!empty($hasil)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Judul</th>
                                            <th>Pengarang</th>
                                            <th>Kategori</th>
                                            <th>Harga</th>
                                            <th>Tahun</th>
                                            <th>Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($hasil_halaman as $buku): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary"><?php echo htmlspecialchars($buku['kode']); ?></span>
                                                </td>
                                                <td>
                                                    <strong>
                                                        <?php echo highlightKeyword($buku['judul'], $keyword); ?>
                                                    </strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($buku['penerbit']); ?></small>
                                                </td>
                                                <td>
                                                    <?php echo highlightKeyword($buku['pengarang'], $keyword); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($buku['kategori']); ?></td>
                                                <td>
                                                    <strong>Rp <?php echo number_format($buku['harga'], 0, ',', '.'); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($buku['tahun']); ?></td>
                                                <td>
                                                    <?php if ($buku['stok'] > 0): ?>
                                                        <span class="badge badge-stok badge-tersedia"><?php echo $buku['stok']; ?> tersedia</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-stok badge-habis">Habis</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <!-- Previous -->
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?keyword=<?php echo htmlspecialchars(urlencode($keyword)); ?>&kategori=<?php echo htmlspecialchars(urlencode($kategori)); ?>&min_harga=<?php echo htmlspecialchars(urlencode($min_harga)); ?>&max_harga=<?php echo htmlspecialchars(urlencode($max_harga)); ?>&tahun=<?php echo htmlspecialchars(urlencode($tahun)); ?>&status=<?php echo htmlspecialchars(urlencode($status)); ?>&sort=<?php echo htmlspecialchars(urlencode($sort)); ?>&page=<?php echo $page - 1; ?>">← Sebelumnya</a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Page Numbers -->
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                                <a class="page-link" href="?keyword=<?php echo htmlspecialchars(urlencode($keyword)); ?>&kategori=<?php echo htmlspecialchars(urlencode($kategori)); ?>&min_harga=<?php echo htmlspecialchars(urlencode($min_harga)); ?>&max_harga=<?php echo htmlspecialchars(urlencode($max_harga)); ?>&tahun=<?php echo htmlspecialchars(urlencode($tahun)); ?>&status=<?php echo htmlspecialchars(urlencode($status)); ?>&sort=<?php echo htmlspecialchars(urlencode($sort)); ?>&page=<?php echo $i; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Next -->
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?keyword=<?php echo htmlspecialchars(urlencode($keyword)); ?>&kategori=<?php echo htmlspecialchars(urlencode($kategori)); ?>&min_harga=<?php echo htmlspecialchars(urlencode($min_harga)); ?>&max_harga=<?php echo htmlspecialchars(urlencode($max_harga)); ?>&tahun=<?php echo htmlspecialchars(urlencode($tahun)); ?>&status=<?php echo htmlspecialchars(urlencode($status)); ?>&sort=<?php echo htmlspecialchars(urlencode($sort)); ?>&page=<?php echo $page + 1; ?>">Selanjutnya →</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                <strong>⚠️ Tidak ada hasil</strong> yang sesuai dengan kriteria pencarian Anda.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
