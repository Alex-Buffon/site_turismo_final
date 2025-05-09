<?php
require_once 'includes/header.php';



// Inicializa arrays para armazenar contadores
$contadores = [
    'contatos' => ['total' => 0, 'nao_lidos' => 0],
    'comentarios' => ['total' => 0, 'pendentes' => 0],
    'eventos' => ['total' => 0],
    'servicos' => ['total' => 0],
    'apoiadores' => ['total' => 0],
    'galerias' => ['total' => 0],
    'visitas' => [
        'total_visitas' => 0,
        'visitantes_unicos' => 0,
        'dispositivos_diferentes' => 0,
        'origens_diferentes' => 0,
        'total_acessos' => 0
    ]
];


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


    // Contador de apoiadores
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM apoiadores");
    $apoiadores = $stmt->fetch(PDO::FETCH_ASSOC);

    // Adicionar contador de galeria
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM galeria");
    $galeria = $stmt->fetch(PDO::FETCH_ASSOC);

    // Substitua a consulta de estatísticas atual por esta:
    $stmt = $pdo->query("SELECT
COUNT(*) as total_visitas,
MAX(data_ultima_visita) as ultima_visita,
COUNT(CASE WHEN DATE(data_visita) = CURRENT_DATE THEN 1 END) as visitas_hoje,
COUNT(DISTINCT ip_visitante) as visitantes_unicos,
SUM(contador) as total_acessos,
COUNT(DISTINCT dispositivo) as dispositivos_diferentes,
COUNT(DISTINCT pagina_origem) as origens_diferentes
FROM visitas");
    $visitas = $stmt->fetch(PDO::FETCH_ASSOC);


    // Adicionar contador de rastreamento
    $stmt = $pdo->query("
SELECT
COUNT(*) as total_cliques,
COUNT(DISTINCT ip_visitante) as visitantes_unicos,
COUNT(DISTINCT tipo) as tipos_diferentes
FROM rastreamento_cliques
");
    $rastreamento = $stmt->fetch(PDO::FETCH_ASSOC);
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

<!-- Adicionar card da galeria -->
<div class="card card-galerias">
    <div class="card-icon">
        <i class="fas fa-images"></i>
    </div>
    <div class="card-info">
        <h3>Galeria</h3>
        <p>
            <span class="total-count"><?php echo $galerias['total'] ?? 0; ?></span>
        </p>
    </div>
</div>
</div>

<!-- Adicione após os cards existentes na div stats-grid -->
<div class="stat-card">
    <div class="stat-header">
        <h3>Visitantes Únicos</h3>
    </div>
    <div class="stat-body">
        <div class="stat-number"><?php echo number_format($visitas['visitantes_unicos'] ?? 0, 0, ',', '.'); ?></div>
        <div class="stat-label">IPs diferentes</div>
    </div>
    <div class="stat-footer">
        Dispositivos: <?php echo $visitas['dispositivos_diferentes'] ?? 0; ?> diferentes
    </div>
</div>

<div class="stat-card">
    <div class="stat-header">
        <h3>Origens de Acesso</h3>
    </div>
    <div class="stat-body">
        <div class="stat-number"><?php echo $visitas['origens_diferentes'] ?? 0; ?></div>
        <div class="stat-label">Fontes de tráfego</div>
    </div>
    <div class="stat-footer">
        Total de acessos: <?php echo number_format($visitas['total_acessos'] ?? 0, 0, ',', '.'); ?>
    </div>
</div>







<?php require_once 'includes/footer.php'; ?>
