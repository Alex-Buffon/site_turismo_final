<?php
require_once '../includes/header.php';

// Buscar estatísticas de visitas
try {
    $stmt = $pdo->query("SELECT
        v.*,
        DATE_FORMAT(v.ultima_visita, '%d/%m/%Y às %H:%i') as data_formatada
    FROM visitas v
    ORDER BY v.contador DESC");
    $estatisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao buscar estatísticas: " . $e->getMessage();
}
?>

<div class="content-header">
    <h2><i class="fas fa-chart-line"></i> Estatísticas de Visitas</h2>
</div>

<div class="content-body">
    <?php if (isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php else: ?>
        <div class="stats-grid">
            <?php foreach($estatisticas as $stat): ?>
                <div class="stat-card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="card-info">
                        <h3><?php echo ucfirst($stat['pagina']); ?></h3>
                        <p class="stat-number"><?php echo number_format($stat['contador'], 0, ',', '.'); ?></p>
                        <p class="stat-footer">
                            Última visita:<br>
                            <?php echo $stat['data_formatada']; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
