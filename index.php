<?php
require 'config/koneksi.php';
$query = mysqli_query($conn, "SELECT * FROM absensi ORDER BY id DESC");

// Hitung statistik
$total = mysqli_num_rows($query);
mysqli_data_seek($query, 0);

$hadir = $izin = $sakit = $alpa = 0;
$temp = [];
while($row = mysqli_fetch_assoc($query)) {
    $temp[] = $row;
    switch($row['status_kehadiran']) {
        case 'Hadir': $hadir++; break;
        case 'Izin':  $izin++;  break;
        case 'Sakit': $sakit++; break;
        case 'Alpa':  $alpa++;  break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Siswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.18);
            --glass-blur: blur(24px);
            --glass-shadow: 0 8px 40px rgba(0,0,0,0.35);
            --accent-1: #a78bfa;
            --accent-2: #38bdf8;
            --accent-3: #f472b6;
            --text-primary: rgba(255,255,255,0.95);
            --text-secondary: rgba(255,255,255,0.55);
            --radius-xl: 24px;
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            color: var(--text-primary);
            background: #07050f;
            overflow-x: hidden;
        }

        /* ─── Animated Background ─── */
        .bg-canvas {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
        }
        .bg-canvas::before, .bg-canvas::after, .blob-3 {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .bg-canvas::before {
            width: 60vw; height: 60vw;
            background: radial-gradient(circle, #5b21b6, transparent 70%);
            top: -20%; left: -15%;
        }
        .bg-canvas::after {
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, #0c4a6e, transparent 70%);
            bottom: -20%; right: -10%;
            animation-delay: -6s;
        }
        .blob-3 {
            width: 40vw; height: 40vw;
            background: radial-gradient(circle, #831843, transparent 70%);
            top: 40%; left: 50%; transform: translate(-50%, -50%);
            animation-delay: -3s;
        }
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(4vw, 6vh) scale(1.08); }
        }

        /* ─── Layout ─── */
        .page-wrap {
            position: relative; z-index: 1;
            padding: 2rem 1rem 4rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* ─── Header ─── */
        .page-header {
            text-align: center;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease both;
        }
        .page-header .eyebrow {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--accent-1);
            margin-bottom: 0.5rem;
        }
        .page-header h1 {
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, var(--accent-1) 60%, var(--accent-2) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        /* ─── Glass Card ─── */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow);
        }

        /* ─── Stats Row ─── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s 0.1s ease both;
        }
        @media (max-width: 600px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-lg);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.4); }
        .stat-card .stat-num {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-top: 0.3rem;
        }
        .stat-hadir .stat-num { color: #4ade80; }
        .stat-izin  .stat-num { color: #60a5fa; }
        .stat-sakit .stat-num { color: #fbbf24; }
        .stat-alpa  .stat-num { color: #f87171; }

        /* ─── Form Card ─── */
        .form-card {
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s 0.2s ease both;
        }
        .form-card .section-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        .input-group-custom {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 0.75rem;
            align-items: start;
        }
        @media (max-width: 768px) {
            .input-group-custom { grid-template-columns: 1fr 1fr; }
            .input-group-custom .btn-tambah { grid-column: span 2; }
        }
        @media (max-width: 480px) {
            .input-group-custom { grid-template-columns: 1fr; }
            .input-group-custom .btn-tambah { grid-column: 1; }
        }

        .field-wrap { display: flex; flex-direction: column; gap: 0.35rem; }
        .field-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-secondary);
        }
        .glass-input {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            padding: 0.65rem 1rem;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            width: 100%;
        }
        .glass-input::placeholder { color: rgba(255,255,255,0.3); }
        .glass-input:focus {
            outline: none;
            background: rgba(255,255,255,0.12);
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.2);
        }
        .glass-input option { background: #1e1b2e; color: white; }

        .btn-tambah {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            align-self: flex-end;
        }
        .btn-tambah:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(167,139,250,0.4); }
        .btn-tambah:active { transform: translateY(0); }

        /* ─── Table Card ─── */
        .table-card {
            padding: 1.75rem;
            animation: fadeUp 0.6s 0.3s ease both;
        }
        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .table-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }
        .count-badge {
            background: rgba(167,139,250,0.2);
            border: 1px solid rgba(167,139,250,0.3);
            color: var(--accent-1);
            border-radius: 20px;
            padding: 0.2rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* ─── Responsive Table ─── */
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.4rem;
            font-size: 0.88rem;
        }
        thead th {
            color: var(--text-secondary);
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }
        tbody tr {
            background: rgba(255,255,255,0.05);
            border-radius: var(--radius-md);
            transition: background 0.15s ease;
        }
        tbody tr:hover { background: rgba(255,255,255,0.09); }
        tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }
        tbody td:first-child { border-radius: var(--radius-md) 0 0 var(--radius-md); }
        tbody td:last-child  { border-radius: 0 var(--radius-md) var(--radius-md) 0; }

        .no-col { color: var(--text-secondary); font-size: 0.8rem; width: 40px; }
        .name-col { font-weight: 600; }
        .class-col { color: var(--text-secondary); font-size: 0.82rem; }
        .time-col { color: var(--text-secondary); font-size: 0.78rem; white-space: nowrap; }

        /* ─── Status Badge ─── */
        .badge-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
        }
        .badge-hadir { background: rgba(74,222,128,0.15); color: #4ade80; border: 1px solid rgba(74,222,128,0.25); }
        .badge-izin  { background: rgba(96,165,250,0.15); color: #60a5fa; border: 1px solid rgba(96,165,250,0.25); }
        .badge-sakit { background: rgba(251,191,36,0.15);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.25); }
        .badge-alpa  { background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid rgba(248,113,113,0.25); }

        /* ─── Action Buttons ─── */
        .action-btns { display: flex; gap: 0.4rem; justify-content: flex-end; }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.85rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-edit {
            background: rgba(251,191,36,0.12);
            border-color: rgba(251,191,36,0.25);
            color: #fbbf24;
        }
        .btn-edit:hover { background: rgba(251,191,36,0.25); color: #fbbf24; transform: translateY(-1px); }
        .btn-delete {
            background: rgba(248,113,113,0.12);
            border-color: rgba(248,113,113,0.25);
            color: #f87171;
        }
        .btn-delete:hover { background: rgba(248,113,113,0.25); color: #f87171; transform: translateY(-1px); }

        /* ─── Empty State ─── */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 0.75rem; opacity: 0.4; }
        .empty-state p { font-size: 0.9rem; }

        /* ─── Mobile Card View (< 540px) ─── */
        @media (max-width: 540px) {
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tbody tr {
                margin-bottom: 0.75rem;
                border-radius: var(--radius-lg);
                padding: 0.75rem 1rem;
            }
            tbody td { padding: 0.25rem 0; border-radius: 0 !important; }
            tbody td::before {
                content: attr(data-label) ': ';
                font-size: 0.65rem;
                font-weight: 600;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--text-secondary);
                display: inline-block;
                margin-right: 0.5rem;
            }
            .no-col { display: none; }
            .action-btns { justify-content: flex-start; margin-top: 0.5rem; }
        }

        /* ─── Animations ─── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }

        /* ─── Delete Modal ─── */
        .modal-overlay {
            position: fixed; inset: 0; z-index: 100;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
            opacity: 0; pointer-events: none;
            transition: opacity 0.2s ease;
        }
        .modal-overlay.show { opacity: 1; pointer-events: all; }

        .modal-box {
            background: rgba(30, 20, 60, 0.85);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(248,113,113,0.25);
            border-radius: var(--radius-xl);
            padding: 2rem 1.75rem 1.75rem;
            max-width: 360px; width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.05);
            transform: scale(0.94) translateY(10px);
            transition: transform 0.25s ease;
            text-align: center;
        }
        .modal-overlay.show .modal-box { transform: scale(1) translateY(0); }

        .modal-icon {
            width: 56px; height: 56px;
            background: rgba(248,113,113,0.15);
            border: 1px solid rgba(248,113,113,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1.25rem;
        }
        .modal-title {
            font-size: 1.1rem; font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .modal-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }
        .modal-desc strong { color: var(--text-primary); }
        .modal-actions { display: flex; gap: 0.75rem; }
        .btn-cancel {
            flex: 1;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-cancel:hover { background: rgba(255,255,255,0.14); color: var(--text-primary); }
        .btn-confirm-delete {
            flex: 1;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s ease;
        }
        .btn-confirm-delete:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(239,68,68,0.4); color: white; }
    </style>
</head>
<body>
    <div class="bg-canvas"><div class="blob-3"></div></div>

    <div class="page-wrap">

        <!-- Header -->
        <div class="page-header">
            <div class="eyebrow">✦ Sistem Manajemen</div>
            <h1>Absensi Siswa</h1>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="glass stat-card stat-hadir">
                <div class="stat-num"><?= $hadir ?></div>
                <div class="stat-label">Hadir</div>
            </div>
            <div class="glass stat-card stat-izin">
                <div class="stat-num"><?= $izin ?></div>
                <div class="stat-label">Izin</div>
            </div>
            <div class="glass stat-card stat-sakit">
                <div class="stat-num"><?= $sakit ?></div>
                <div class="stat-label">Sakit</div>
            </div>
            <div class="glass stat-card stat-alpa">
                <div class="stat-num"><?= $alpa ?></div>
                <div class="stat-label">Alpa</div>
            </div>
        </div>

        <!-- Form Tambah -->
        <div class="glass form-card">
            <div class="section-label">Tambah Data Baru</div>
            <form action="tambah.php" method="POST">
                <div class="input-group-custom">
                    <div class="field-wrap">
                        <label class="field-label">Nama Siswa</label>
                        <input type="text" name="nama" class="glass-input" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="field-wrap">
                        <label class="field-label">Kelas</label>
                        <input type="text" name="kelas" class="glass-input" placeholder="Contoh: XII RPL 1" required>
                    </div>
                    <div class="field-wrap">
                        <label class="field-label">Status</label>
                        <select name="status" class="glass-input">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-tambah">+ Tambah</button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="glass table-card">
            <div class="table-header-row">
                <span class="table-title">Data Absensi</span>
                <span class="count-badge"><?= $total ?> Siswa</span>
            </div>

            <?php if (empty($temp)): ?>
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>Belum ada data absensi.<br>Tambahkan data menggunakan form di atas.</p>
            </div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($temp as $data): ?>
                        <tr>
                            <td class="no-col" data-label="No"><?= $no++ ?></td>
                            <td class="name-col" data-label="Nama"><?= htmlspecialchars($data['nama']) ?></td>
                            <td class="class-col" data-label="Kelas"><?= htmlspecialchars($data['kelas']) ?></td>
                            <td data-label="Status">
                                <?php
                                $s = $data['status_kehadiran'];
                                $cls = strtolower($s);
                                echo "<span class='badge-status badge-$cls'>$s</span>";
                                ?>
                            </td>
                            <td class="time-col" data-label="Waktu"><?= htmlspecialchars($data['tanggal']) ?></td>
                            <td data-label="Aksi">
                                <div class="action-btns">
                                    <a href="edit.php?id=<?= $data['id'] ?>" class="btn-action btn-edit">✏ Edit</a>
                                    <button class="btn-action btn-delete" onclick="openDeleteModal(<?= $data['id'] ?>, '<?= addslashes(htmlspecialchars($data['nama'])) ?>', '<?= addslashes(htmlspecialchars($data['kelas'])) ?>')">✕ Hapus</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">🗑️</div>
            <div class="modal-title">Hapus Data?</div>
            <div class="modal-desc">
                Yakin mau hapus data <strong id="modal-nama"></strong> dari kelas <strong id="modal-kelas"></strong>?
                <br>Tindakan ini tidak bisa dibatalkan.
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <a href="#" id="modal-confirm-btn" class="btn-confirm-delete">Ya, Hapus</a>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(id, nama, kelas) {
            document.getElementById('modal-nama').textContent = nama;
            document.getElementById('modal-kelas').textContent = kelas;
            document.getElementById('modal-confirm-btn').href = 'hapus.php?id=' + id;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        // Tutup modal kalau klik di luar box
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        // Tutup modal pakai Esc
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
</body>
</html>