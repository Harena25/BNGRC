<?php
// $distributions provided by controller
?>
<h2>Historique des distributions</h2>
<?php if (empty($distributions)): ?>
    <div class="alert alert-info">Aucune distribution trouvée.</div>
<?php else: ?>
    <div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Article</th>
                <th>Ville</th>
                <th>Quantité</th>
                <th>Besoins (id)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($distributions as $d): ?>
            <tr>
                <td><?php echo htmlspecialchars($d['id']); ?></td>
                <td><?php echo htmlspecialchars($d['date_distribution']); ?></td>
                <td><?php echo htmlspecialchars($d['article_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($d['ville_name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($d['quantite_distribuee']); ?></td>
                <td><?php echo htmlspecialchars($d['besoin_id']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endif; ?>
