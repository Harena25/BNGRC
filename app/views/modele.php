<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BNGRC</title>
        <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
        <style>
                body { padding-top: 56px; }
                .sidebar { min-height: calc(100vh - 112px); }
        </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">BNGRC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/articles">Articles</a></li>
                <li class="nav-item"><a class="nav-link" href="/dons">Dons</a></li>
                <li class="nav-item"><a class="nav-link" href="/autoDistribution">AutoDistribution</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page layout -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 bg-light sidebar p-3">
            <h5>Menu</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/articles">Articles</a></li>
                <li class="nav-item"><a class="nav-link" href="/dons">Dons</a></li>
                <li class="nav-item"><a class="nav-link" href="/autoDistribution">Distribution</a></li>
            </ul>
        </aside>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 p-4">
            <?php include __DIR__ . '/' . $pagename; ?>
        </main>
    </div>
</div>

<!-- Footer -->
<footer class="bg-light text-center py-3 mt-4">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> BNGRC — Suivi des collectes et distributions</small>
    </div>
</footer>

<script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>