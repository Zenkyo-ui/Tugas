<?php
require 'config/koneksi.php';
$id = $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM absensi WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(isset($_POST['update'])) {
    $nama   = $_POST['nama'];
    $kelas  = $_POST['kelas'];
    $status = $_POST['status'];

    $update = mysqli_prepare($conn, "UPDATE absensi SET nama=?, kelas=?, status_kehadiran=? WHERE id=?");
    mysqli_stmt_bind_param($update, "sssi", $nama, $kelas, $status, $id);
    mysqli_stmt_execute($update);

    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255,255,255,0.08);
            --glass-border: rgba(255,255,255,0.18);
            --accent-1: #a78bfa;
            --accent-2: #38bdf8;
            --text-primary: rgba(255,255,255,0.95);
            --text-secondary: rgba(255,255,255,0.55);
            --radius-xl: 24px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: #07050f;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-x: hidden;
        }

        /* ─── Background ─── */
        .bg-canvas {
            position: fixed; inset: 0; z-index: 0; overflow: hidden; pointer-events: none;
        }
        .bg-canvas::before, .bg-canvas::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.45;
            animation: drift 10s ease-in-out infinite alternate;
        }
        .bg-canvas::before {
            width: 55vw; height: 55vw;
            background: radial-gradient(circle, #5b21b6, transparent 70%);
            top: -15%; left: -10%;
        }
        .bg-canvas::after {
            width: 45vw; height: 45vw;
            background: radial-gradient(circle, #0c4a6e, transparent 70%);
            bottom: -20%; right: -10%;
            animation-delay: -5s;
        }
        @keyframes drift {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(3vw, 5vh) scale(1.06); }
        }

        /* ─── Card ─── */
        .edit-card {
            position: relative; z-index: 1;
            width: 100%;
            max-width: 480px;
            background: var(--glass-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: 0 8px 40px rgba(0,0,0,0.4);
            padding: 2.25rem 2rem;
            animation: slideUp 0.5s ease both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ─── Back link ─── */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 1.75rem;
            transition: color 0.2s ease;
        }
        .back-link:hover { color: var(--text-primary); }
        .back-link .arrow { font-size: 1rem; }

        /* ─── Title ─── */
        .card-eyebrow {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--accent-1);
            margin-bottom: 0.35rem;
        }
        .card-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 20%, var(--accent-1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ─── Fields ─── */
        .field {
            margin-bottom: 1.25rem;
        }
        .field label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        .glass-input {
            width: 100%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            appearance: none;
        }
        .glass-input::placeholder { color: rgba(255,255,255,0.28); }
        .glass-input:focus {
            outline: none;
            background: rgba(255,255,255,0.11);
            border-color: var(--accent-1);
            box-shadow: 0 0 0 3px rgba(167,139,250,0.2);
        }
        .glass-input option { background: #1a1730; color: white; }

        /* ─── Status Selector ─── */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .status-opt { display: none; }
        .status-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 0.4rem;
            border-radius: 10px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.05);
            color: var(--text-secondary);
            transition: all 0.15s ease;
            text-align: center;
        }
        .status-label:hover { background: rgba(255,255,255,0.1); color: var(--text-primary); }
        .status-opt[value="Hadir"]:checked  ~ .status-label-hadir  { background: rgba(74,222,128,0.18);  border-color: #4ade80; color: #4ade80; }
        .status-opt[value="Izin"]:checked   ~ .status-label-izin   { background: rgba(96,165,250,0.18);  border-color: #60a5fa; color: #60a5fa; }
        .status-opt[value="Sakit"]:checked  ~ .status-label-sakit  { background: rgba(251,191,36,0.18);  border-color: #fbbf24; color: #fbbf24; }
        .status-opt[value="Alpa"]:checked   ~ .status-label-alpa   { background: rgba(248,113,113,0.18); border-color: #f87171; color: #f87171; }

        /* ─── Divider ─── */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 1.75rem 0 1.5rem;
        }

        /* ─── Buttons ─── */
        .btn-save {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            padding: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: 0.02em;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(167,139,250,0.45); }
        .btn-save:active { transform: translateY(0); }

        /* ─── Status hidden select ─── */
        .status-hidden { display: none; }
    </style>
</head>
<body>
    <div class="bg-canvas"></div>

    <div class="edit-card">
        <a href="index.php" class="back-link"><span class="arrow">←</span> Kembali</a>

        <div class="card-eyebrow">✦ Edit Data</div>
        <div class="card-title">Ubah Absensi</div>

        <form method="POST" id="editForm">
            <div class="field">
                <label>Nama Siswa</label>
                <input type="text" name="nama" class="glass-input"
                       value="<?= htmlspecialchars($data['nama']) ?>" required
                       placeholder="Nama lengkap siswa">
            </div>

            <div class="field">
                <label>Kelas</label>
                <input type="text" name="kelas" class="glass-input"
                       value="<?= htmlspecialchars($data['kelas']) ?>" required
                       placeholder="Contoh: XII RPL 1">
            </div>

            <div class="field">
                <label>Status Kehadiran</label>
                <?php $cur = $data['status_kehadiran']; ?>
                <!-- Visible toggle -->
                <div class="status-grid">
                    <label style="position:relative; display:flex; flex-direction:column; align-items:center; gap:0.2rem;">
                        <input type="radio" name="status" value="Hadir" class="status-opt" <?= $cur=='Hadir'?'checked':'' ?> onchange="syncStatus(this)">
                        <span class="status-label" id="lbl-Hadir" style="width:100%; justify-content:center; border-color:<?= $cur=='Hadir'?'#4ade80':'rgba(255,255,255,0.12)' ?>; color:<?= $cur=='Hadir'?'#4ade80':'rgba(255,255,255,0.5)' ?>; background:<?= $cur=='Hadir'?'rgba(74,222,128,0.18)':'rgba(255,255,255,0.05)' ?>;">✓ Hadir</span>
                    </label>
                    <label style="position:relative; display:flex; flex-direction:column; align-items:center; gap:0.2rem;">
                        <input type="radio" name="status" value="Izin" class="status-opt" <?= $cur=='Izin'?'checked':'' ?> onchange="syncStatus(this)">
                        <span class="status-label" id="lbl-Izin" style="width:100%; justify-content:center; border-color:<?= $cur=='Izin'?'#60a5fa':'rgba(255,255,255,0.12)' ?>; color:<?= $cur=='Izin'?'#60a5fa':'rgba(255,255,255,0.5)' ?>; background:<?= $cur=='Izin'?'rgba(96,165,250,0.18)':'rgba(255,255,255,0.05)' ?>;">📄 Izin</span>
                    </label>
                    <label style="position:relative; display:flex; flex-direction:column; align-items:center; gap:0.2rem;">
                        <input type="radio" name="status" value="Sakit" class="status-opt" <?= $cur=='Sakit'?'checked':'' ?> onchange="syncStatus(this)">
                        <span class="status-label" id="lbl-Sakit" style="width:100%; justify-content:center; border-color:<?= $cur=='Sakit'?'#fbbf24':'rgba(255,255,255,0.12)' ?>; color:<?= $cur=='Sakit'?'#fbbf24':'rgba(255,255,255,0.5)' ?>; background:<?= $cur=='Sakit'?'rgba(251,191,36,0.18)':'rgba(255,255,255,0.05)' ?>;">🤒 Sakit</span>
                    </label>
                    <label style="position:relative; display:flex; flex-direction:column; align-items:center; gap:0.2rem;">
                        <input type="radio" name="status" value="Alpa" class="status-opt" <?= $cur=='Alpa'?'checked':'' ?> onchange="syncStatus(this)">
                        <span class="status-label" id="lbl-Alpa" style="width:100%; justify-content:center; border-color:<?= $cur=='Alpa'?'#f87171':'rgba(255,255,255,0.12)' ?>; color:<?= $cur=='Alpa'?'#f87171':'rgba(255,255,255,0.5)' ?>; background:<?= $cur=='Alpa'?'rgba(248,113,113,0.18)':'rgba(255,255,255,0.05)' ?>;">✗ Alpa</span>
                    </label>
                </div>
            </div>

            <hr class="divider">

            <button type="submit" name="update" class="btn-save">Simpan Perubahan</button>
        </form>
    </div>

    <script>
        const colors = {
            Hadir: { border: '#4ade80', color: '#4ade80', bg: 'rgba(74,222,128,0.18)' },
            Izin:  { border: '#60a5fa', color: '#60a5fa', bg: 'rgba(96,165,250,0.18)' },
            Sakit: { border: '#fbbf24', color: '#fbbf24', bg: 'rgba(251,191,36,0.18)' },
            Alpa:  { border: '#f87171', color: '#f87171', bg: 'rgba(248,113,113,0.18)' },
        };
        const inactive = { border: 'rgba(255,255,255,0.12)', color: 'rgba(255,255,255,0.5)', bg: 'rgba(255,255,255,0.05)' };

        function syncStatus(radio) {
            ['Hadir','Izin','Sakit','Alpa'].forEach(v => {
                const lbl = document.getElementById('lbl-' + v);
                const c = (v === radio.value) ? colors[v] : inactive;
                lbl.style.borderColor  = c.border;
                lbl.style.color        = c.color;
                lbl.style.background   = c.bg;
            });
        }
    </script>
</body>
</html>