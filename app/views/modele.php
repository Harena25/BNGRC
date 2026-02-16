<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC — Suivi des collectes</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
    <style>
        :root {
            --bonbon-1: #f7dae7;
            --bonbon-2: #e2b4c1;
            --bonbon-3: #d38c9d;
            --bonbon-4: #a55166;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fdf5f8;
            color: #3a2a30;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            background: linear-gradient(135deg, var(--bonbon-4), var(--bonbon-3));
            color: #fff;
            height: 56px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 1.2rem;
            box-shadow: 0 2px 8px rgba(165, 81, 102, .25);
        }

        .topbar .brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 1px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .topbar .brand i {
            font-size: 1.4rem;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-right a {
            color: rgba(255, 255, 255, .85);
            text-decoration: none;
            font-size: .9rem;
            transition: color .2s;
        }

        .topbar-right a:hover {
            color: #fff;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.4rem;
            cursor: pointer;
            display: none;
            margin-right: .8rem;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 240px;
            height: calc(100vh - 56px);
            background: #fff;
            border-right: 1px solid var(--bonbon-1);
            overflow-y: auto;
            z-index: 1020;
            padding: 1.2rem 0;
            transition: transform .3s ease;
        }

        .sidebar-header {
            padding: 0 1.2rem .8rem;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--bonbon-3);
            font-weight: 700;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .6rem 1.2rem;
            color: #5a3a44;
            font-size: .92rem;
            font-weight: 500;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .2s;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            color: var(--bonbon-3);
        }

        .sidebar .nav-link:hover {
            background: var(--bonbon-1);
            border-left-color: var(--bonbon-4);
            color: var(--bonbon-4);
        }

        .sidebar .nav-link:hover i {
            color: var(--bonbon-4);
        }

        .sidebar .nav-link.active {
            background: var(--bonbon-1);
            border-left-color: var(--bonbon-4);
            color: var(--bonbon-4);
            font-weight: 600;
        }

        .sidebar .nav-link.active i {
            color: var(--bonbon-4);
        }

        .sidebar-divider {
            height: 1px;
            background: var(--bonbon-1);
            margin: .6rem 1.2rem;
        }

        /* ─── MAIN ─── */
        .main-wrapper {
            margin-top: 56px;
            margin-left: 240px;
            flex: 1;
            padding: 1.5rem 2rem;
            min-height: calc(100vh - 56px - 48px);
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-left: 240px;
            background: #fff;
            border-top: 1px solid var(--bonbon-1);
            text-align: center;
            padding: .75rem 1rem;
            font-size: .82rem;
            color: var(--bonbon-3);
        }

        /* ─── OVERRIDE BOOTSTRAP COMPONENTS ─── */
        .btn-primary {
            background: var(--bonbon-4);
            border-color: var(--bonbon-4);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--bonbon-3);
            border-color: var(--bonbon-3);
        }

        .btn-outline-primary {
            color: var(--bonbon-4);
            border-color: var(--bonbon-4);
        }

        .btn-outline-primary:hover {
            background: var(--bonbon-4);
            border-color: var(--bonbon-4);
            color: #fff;
        }

        .card {
            border: 1px solid var(--bonbon-1);
            border-radius: .6rem;
        }

        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--bonbon-4), var(--bonbon-3)) !important;
            border-bottom: none;
        }

        .table-dark {
            --bs-table-bg: var(--bonbon-4);
            --bs-table-border-color: var(--bonbon-3);
        }

        .badge.bg-info {
            background: var(--bonbon-2) !important;
            color: #5a3a44;
        }

        .badge.bg-success {
            background: var(--bonbon-3) !important;
        }

        .alert-success {
            background: var(--bonbon-1);
            border-color: var(--bonbon-2);
            color: var(--bonbon-4);
        }

        .alert-danger {
            background: #fce4e4;
            border-color: #e2aaaa;
            color: #8b3a3a;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--bonbon-3);
            box-shadow: 0 0 0 .2rem rgba(165, 81, 102, .18);
        }

        a {
            color: var(--bonbon-4);
        }

        a:hover {
            color: var(--bonbon-3);
        }

        .shadow-sm {
            box-shadow: 0 2px 8px rgba(165, 81, 102, .08) !important;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 991.98px) {
            .sidebar-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 4px 0 16px rgba(0, 0, 0, .15);
            }

            .main-wrapper,
            .footer {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- ═══ TOPBAR ═══ -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <a class="brand" href="/">
            <i class="bi bi-heart-pulse-fill"></i> BNGRC
        </a>
        <div class="topbar-right">
            <a href="/stock"><i class="bi bi-box-seam"></i> Stock</a>
            <a href="/dons"><i class="bi bi-gift"></i> Dons</a>
            <a href="/distribution"><i class="bi bi-truck"></i> Distribution</a>
        </div>
    </header>

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">Navigation</div>
        <nav>
            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>"
                href="/dashboard">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-header">Gestion</div>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/articles') !== false ? 'active' : ''; ?>"
                href="/articles">
                <i class="bi bi-box-seam"></i> Articles
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? 'active' : ''; ?>"
                href="/dons">
                <i class="bi bi-gift"></i> Dons
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/stock') !== false ? 'active' : ''; ?>"
                href="/stock">
                <i class="bi bi-box-seam"></i> Stock
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/needs/list') !== false ? 'active' : ''; ?>"
                href="/needs/list">
                <i class="bi bi-card-checklist"></i> Besoins
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-header">Opérations</div>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/distribution') !== false ? 'active' : ''; ?>"
                href="/distribution">
                <i class="bi bi-truck"></i> Distribution
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/cities') !== false ? 'active' : ''; ?>"
                href="/cities">
                <i class="bi bi-geo-alt"></i> Villes
            </a>
        </nav>
    </aside>

    <!-- ═══ MAIN CONTENT ═══ -->
    <main class="main-wrapper">
        <?php include __DIR__ . '/' . $pagename; ?>
    </main>

    <!-- ═══ FOOTER ═══ -->
    <footer class="footer">
        <i class="bi bi-heart-fill" style="color:var(--bonbon-3);"></i>
        &copy; <?php echo date('Y'); ?> BNGRC &mdash; Suivi des collectes et distributions de dons
    </footer>

    <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle mobile
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        toggle.addEventListener('click', () => sidebar.classList.toggle('show'));

        // Fermer sidebar quand on clique en dehors (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 992 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>