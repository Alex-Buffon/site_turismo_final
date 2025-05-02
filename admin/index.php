<?php
require_once 'includes/header.php';

// Busca dados para o dashboard
try {
    // Contadores de contatos
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'não lido' THEN 1 ELSE 0 END) as nao_lidos
        FROM contatos");
    $contatos = $stmt->fetch(PDO::FETCH_ASSOC);

    // Contadores de comentários
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes
        FROM comentarios");
    $comentarios = $stmt->fetch(PDO::FETCH_ASSOC);

    // Contador de eventos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM eventos");
    $eventos = $stmt->fetch(PDO::FETCH_ASSOC);

    // Contador de serviços
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM servicos");
    $servicos = $stmt->fetch(PDO::FETCH_ASSOC);

    // Contador de apoiadores
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM apoiadores");
    $apoiadores = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao carregar dados: " . $e->getMessage();
    exit;
}
?>

<div class="content-header">
    <h1>Dashboard</h1>
</div>

<div class="dashboard-cards">
    <div class="card card-contatos">
        <div class="card-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="card-info">
            <h3>Contatos</h3>
            <p>
                <span class="total-count"><?php echo $contatos['total'] ?? 0; ?></span>
                <?php if (isset($contatos['nao_lidos']) && $contatos['nao_lidos'] > 0): ?>
                    <span class="badge badge-warning"><?php echo $contatos['nao_lidos']; ?> não lidos</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="card card-comentarios">
        <div class="card-icon">
            <i class="fas fa-comments"></i>
        </div>
        <div class="card-info">
            <h3>Comentários</h3>
            <p>
                <span class="total-count"><?php echo $comentarios['total'] ?? 0; ?></span>
                <?php if (isset($comentarios['pendentes']) && $comentarios['pendentes'] > 0): ?>
                    <span class="badge badge-warning"><?php echo $comentarios['pendentes']; ?> pendentes</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="card card-eventos">
        <div class="card-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="card-info">
            <h3>Eventos</h3>
            <p>
                <span class="total-count"><?php echo $eventos['total'] ?? 0; ?></span>
            </p>
        </div>
    </div>

    <div class="card card-servicos">
        <div class="card-icon">
            <i class="fas fa-concierge-bell"></i>
        </div>
        <div class="card-info">
            <h3>Serviços</h3>
            <p>
                <span class="total-count"><?php echo $servicos['total'] ?? 0; ?></span>
            </p>
        </div>
    </div>

    <div class="card card-apoiadores">
        <div class="card-icon">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="card-info">
            <h3>Apoiadores</h3>
            <p>
                <span class="total-count"><?php echo $apoiadores['total'] ?? 0; ?></span>
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
