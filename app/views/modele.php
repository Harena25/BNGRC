<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC — Suivi des collectes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/bootstrap-icons/bootstrap-icons.css">
    <link id="themeStylesheet" rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/style/style1.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/style/sidebar-accordion.css">
    <script>
        // Configuration globale pour JavaScript
        window.APP_BASE_PATH = '<?php echo BASE_PATH; ?>';
    </script>
</head>

<body>

    <!-- ═══ TOPBAR ═══ -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <a class="brand" href="<?php echo BASE_PATH; ?>/">
            <i class="bi bi-heart-pulse-fill"></i> BNGRC
        </a>
        <div class="topbar-right">
            <a href="<?php echo BASE_PATH; ?>/stock"><i class="bi bi-box-seam"></i> Stock</a>
            <a href="<?php echo BASE_PATH; ?>/dons"><i class="bi bi-gift"></i> Dons</a>
            <a href="<?php echo BASE_PATH; ?>/distribution"><i class="bi bi-truck"></i> Distribution</a>
        </div>
    </header>

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">Navigation</div>
        <nav>
            <!-- Accordion Menu -->
            <div class="accordion accordion-flush" id="sidebarAccordion">

                <!-- Section: Tableau de bord -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button <?php echo ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || strpos($_SERVER['REQUEST_URI'], '/recap') !== false) ? '' : 'collapsed'; ?>"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard">
                            <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                        </button>
                    </h2>
                    <div id="collapseDashboard"
                        class="accordion-collapse collapse <?php echo ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || strpos($_SERVER['REQUEST_URI'], '/recap') !== false) ? 'show' : ''; ?>"
                        data-bs-parent="#sidebarAccordion">
                        <div class="accordion-body">
                            <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/dashboard">
                                <i class="bi bi-house-fill"></i> Accueil
                            </a>
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/recap') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/recap">
                                <i class="bi bi-journal-text"></i> Récapitulatif
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-divider"></div>

                <!-- Section: Gestion -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button <?php echo (strpos($_SERVER['REQUEST_URI'], '/articles') !== false || strpos($_SERVER['REQUEST_URI'], '/dons') !== false || strpos($_SERVER['REQUEST_URI'], '/stock') !== false || strpos($_SERVER['REQUEST_URI'], '/needs') !== false) ? '' : 'collapsed'; ?>"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGestion">
                            <i class="bi bi-folder-fill me-2"></i> Gestion
                        </button>
                    </h2>
                    <div id="collapseGestion"
                        class="accordion-collapse collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/articles') !== false || strpos($_SERVER['REQUEST_URI'], '/dons') !== false || strpos($_SERVER['REQUEST_URI'], '/stock') !== false || strpos($_SERVER['REQUEST_URI'], '/needs') !== false) ? 'show' : ''; ?>"
                        data-bs-parent="#sidebarAccordion">
                        <div class="accordion-body">
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/articles') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/articles">
                                <i class="bi bi-box-seam"></i> Articles
                            </a>
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/dons">
                                <i class="bi bi-gift"></i> Dons
                            </a>
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/stock') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/stock">
                                <i class="bi bi-box-seam"></i> Stock
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section: Besoins -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button <?php echo (strpos($_SERVER['REQUEST_URI'], '/needs') !== false) ? '' : 'collapsed'; ?>"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBesoins">
                            <i class="bi bi-card-checklist me-2"></i> Besoins
                        </button>
                    </h2>
                    <div id="collapseBesoins"
                        class="accordion-collapse collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/needs') !== false) ? 'show' : ''; ?>"
                        data-bs-parent="#sidebarAccordion">
                        <div class="accordion-body">
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/needs/list') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/needs/list">
                                <i class="bi bi-list-ul"></i> Besoins général
                            </a>
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/needs/restants') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/needs/restants">
                                <i class="bi bi-exclamation-triangle-fill"></i> Besoins restants
                            </a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-divider"></div>
                <div class="sidebar-header">Opérations</div>

                <!-- Section: Opérations -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button
                            class="accordion-button <?php echo (strpos($_SERVER['REQUEST_URI'], '/distribution') !== false || strpos($_SERVER['REQUEST_URI'], '/cities') !== false || strpos($_SERVER['REQUEST_URI'], '/purchases') !== false || strpos($_SERVER['REQUEST_URI'], '/achats') !== false) ? '' : 'collapsed'; ?>"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseOperations">
                            <i class="bi bi-lightning-fill me-2"></i> Opérations
                        </button>
                    </h2>
                    <div id="collapseOperations"
                        class="accordion-collapse collapse <?php echo (strpos($_SERVER['REQUEST_URI'], '/distribution') !== false || strpos($_SERVER['REQUEST_URI'], '/cities') !== false || strpos($_SERVER['REQUEST_URI'], '/purchases') !== false || strpos($_SERVER['REQUEST_URI'], '/achats') !== false) ? 'show' : ''; ?>"
                        data-bs-parent="#sidebarAccordion">
                        <div class="accordion-body">
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/distribution') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/distribution">
                                <i class="bi bi-truck"></i> Distribution
                            </a>
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/purchases') !== false || strpos($_SERVER['REQUEST_URI'], '/achats') !== false) ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/purchases/list">
                                <i class="bi bi-cart3"></i> Achats
                            </a>
                            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/cities') !== false ? 'active' : ''; ?>"
                                href="<?php echo BASE_PATH; ?>/cities">
                                <i class="bi bi-geo-alt"></i> Villes
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section: Thème -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTheme">
                            <i class="bi bi-palette-fill me-2"></i> Thème
                        </button>
                    </h2>
                    <div id="collapseTheme" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                        <div class="accordion-body">
                            <a href="#" class="nav-link theme-switch"
                                data-theme="<?php echo BASE_PATH; ?>/assets/style/style.css">
                                <i class="bi bi-palette"></i> Bonbon
                            </a>
                            <a href="#" class="nav-link theme-switch"
                                data-theme="<?php echo BASE_PATH; ?>/assets/style/style1.css">
                                <i class="bi bi-palette-fill"></i> Vibrant
                            </a>
                            <a href="#" class="nav-link theme-switch"
                                data-theme="<?php echo BASE_PATH; ?>/assets/style/style2.css">
                                <i class="bi bi-tree-fill"></i> Nature
                            </a>
                            <a href="#" class="nav-link theme-switch"
                                data-theme="<?php echo BASE_PATH; ?>/assets/style/style3.css">
                                <i class="bi bi-flower1"></i> Pastoral
                            </a>
                        </div>
                    </div>
                </div>

            </div>
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

    <script src="<?php echo BASE_PATH; ?>/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
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

        // Fermer sidebar quand on clique sur un lien (mobile)
        document.querySelectorAll('.sidebar .nav-link:not(.theme-switch)').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                }
            });
        });

        // Theme switcher (persists in localStorage)
        (function () {
            const themeLink = document.getElementById('themeStylesheet');
            const defaultTheme = themeLink ? themeLink.getAttribute('href') : '<?php echo BASE_PATH; ?>/assets/style/style.css';
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