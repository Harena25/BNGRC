<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC — Suivi des collectes</title>
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/bootstrap-icons/bootstrap-icons.css">
    <link id="themeStylesheet" rel="stylesheet" href="/assets/style/style1.css">
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
            <a class=" nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/stock') !== false ? 'active' : ''; ?>"
                href="/stock">
                <i class="bi bi-box-seam"></i> Stock
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/needs/list') !== false ? 'active' : ''; ?>"
                href="/needs/list">
                <i class="bi bi-card-checklist"></i> Besoins
            </a>
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/purchases') !== false ? 'active' : ''; ?>"
                href="/purchases/list">
                <i class="bi bi-cart3"></i> Achats
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-header">Opérations</div>

            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/distribution') !== false ? 'active' : ''; ?>"
                href="/distribution">
                <i class="bi bi-truck"></i> Distribution
            </a>
            <a class=" nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/cities') !== false ? 'active' : ''; ?>"
                href="/cities">
                <i class="bi bi-geo-alt"></i> Villes
            </a>
            <div class="sidebar-divider"></div>
            <div class="sidebar-header">Thème</div>
            <nav aria-label="Theme selector" style="padding:0 0 0 0;">
                <a href="#" class="nav-link theme-switch" data-theme="/assets/style/style.css"><i
                        class="bi bi-palette"></i> Bonbon</a>
                <a href="#" class="nav-link theme-switch" data-theme="/assets/style/style1.css"><i
                        class="bi bi-palette-fill"></i> Vibrant</a>
                <a href="#" class="nav-link theme-switch" data-theme="/assets/style/style2.css"><i
                        class="bi bi-tree-fill"></i> Nature</a>
                <a href="#" class="nav-link theme-switch" data-theme="/assets/style/style3.css"><i
                        class="bi bi-flower1"></i> Pastoral</a>
            </nav>
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
        <p>RAKOTOARIVONY Harena Natolotra Sarobidy ETU-3940</p>
        <p>FENOHERYLIANTSOA Ny Aina Andreane ETU-4199</p>
        <p>FANEVA Jedidia ETU-4042</p>
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

        // Theme switcher (persists in localStorage)
        (function () {
            const themeLink = document.getElementById('themeStylesheet');
            const defaultTheme = themeLink ? themeLink.getAttribute('href') : '/assets/style/style.css';
            const saved = localStorage.getItem('bngrc_theme');
            if (saved) {
                themeLink.setAttribute('href', saved);
            }

            function setActiveButton(href) {
                document.querySelectorAll('.theme-switch').forEach(el => {
                    if (el.getAttribute('data-theme') === href) el.classList.add('active'); else el.classList.remove('active');
                });
            }

            setActiveButton(saved || defaultTheme);

            document.querySelectorAll('.theme-switch').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('data-theme');
                    if (!themeLink) return;
                    themeLink.setAttribute('href', href);
                    localStorage.setItem('bngrc_theme', href);
                    setActiveButton(href);
                });
            });
        })();
    </script>
</body>

</html>