<?php
// Ajuste os caminhos dos includes para garantir que estão corretos
$root_path = $_SERVER['DOCUMENT_ROOT'] . '/site_turismo/admin/';
require_once $root_path . 'includes/header.php';
require_once $root_path . 'includes/conexao.php';

// Adiciona verificação de erro para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verifica conexão com o banco
    if (!isset($pdo)) {
        throw new Exception("Erro: Conexão com banco de dados não estabelecida");
    }

    // Verifica se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'rastreamento_cliques'");
    if ($stmt->rowCount() == 0) {
        // Cria a tabela se não existir
        $pdo->exec("CREATE TABLE IF NOT EXISTS rastreamento_cliques (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo VARCHAR(50) NOT NULL,
            item_id INT NOT NULL,
            titulo VARCHAR(255),
            url_destino VARCHAR(512),
            ip_visitante VARCHAR(45),
            dispositivo VARCHAR(255),
            origem VARCHAR(255),
            data_clique TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $mensagem = "Tabela de rastreamento criada. Aguardando primeiros registros.";
    }

    // Buscar estatísticas por tipo
    $stmt = $pdo->query("
        SELECT
            tipo,
            COUNT(*) as total_cliques,
            COUNT(DISTINCT ip_visitante) as visitantes_unicos,
            MAX(data_clique) as ultimo_clique
        FROM rastreamento_cliques
        GROUP BY tipo
        ORDER BY total_cliques DESC
    ");
    $estatisticas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar últimos cliques - Query modificada
    $stmt = $pdo->query("
SELECT
    rc.*,
    CASE
        WHEN rc.tipo = 'galeria-home' THEN 'Galeria Home'
        WHEN rc.tipo = 'galeria-servicos' THEN 'Galeria Serviços'
        WHEN rc.tipo = 'apoiador' THEN 'Apoiador'
        WHEN rc.tipo = 'pagina-servicos' THEN 'Página Serviços'
        WHEN rc.tipo = 'servico' THEN 'Serviço'
        WHEN rc.tipo = 'galeria' THEN 'Galeria'
        ELSE rc.tipo
    END as tipo_formatado
FROM rastreamento_cliques rc
WHERE rc.tipo IN ('galeria-home', 'galeria-servicos', 'apoiador', 'pagina-servicos', 'servico', 'galeria')
ORDER BY rc.data_clique DESC
LIMIT 50
");

    $ultimos_cliques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Debug para verificar os dados
    if (empty($ultimos_cliques)) {
        error_log("DEBUG: Nenhum clique encontrado");
    } else {
        error_log("DEBUG: Encontrados " . count($ultimos_cliques) . " cliques");
    }
} catch (Exception $e) {
    $erro = "Erro: " . $e->getMessage();
    error_log($erro);
}
?>

<!-- Estrutura HTML atualizada -->
<div class="content">
    <div class="content-header">
        <h2><i class="fas fa-chart-line"></i> Rastreamento de Cliques</h2>
    </div>

    <div class="content-body">
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                <p><?php echo $erro; ?></p>
            </div>
        <?php elseif (isset($mensagem)): ?>
            <div class="alert alert-info">
                <p><?php echo $mensagem; ?></p>
            </div>
        <?php else: ?>
            <!-- Estatísticas -->
            <div class="stats-grid">
                <?php if (empty($estatisticas)): ?>
                    <div class="alert alert-info">
                        <p>Nenhum clique registrado ainda.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($estatisticas as $stat): ?>
                        <div class="stat-card">
                            <div class="card-header">
                                <h3><?php echo ucfirst($stat['tipo']); ?></h3>
                            </div>
                            <div class="card-body">
                                <div class="stat-number">
                                    <?php echo number_format($stat['total_cliques'], 0, ',', '.'); ?>
                                </div>
                                <div class="stat-label">cliques totais</div>
                                <div class="stat-details">
                                    <p><?php echo $stat['visitantes_unicos']; ?> visitantes únicos</p>
                                    <?php if ($stat['ultimo_clique']): ?>
                                        <p>Último: <?php echo date('d/m/Y H:i', strtotime($stat['ultimo_clique'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Botão para abrir modal -->
            <div class="mt-4">
                <button class="btn btn-primary" onclick="abrirUltimosCliques()">
                    <i class="fas fa-history"></i> Ver Últimos Cliques
                </button>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- Modal separado do conteúdo principal -->
<div id="modalUltimosCliques" class="rastreamento-modal">
    <div class="rastreamento-modal-dialog">
    <div class="rastreamento-modal-header">
    <h5 class="rastreamento-modal-title">Últimos Cliques Registrados</h5>
    <div class="modal-actions">
        <button class="btn btn-success btn-sm me-2" onclick="exportarDados()">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="button" class="btn-close" onclick="fecharModal()"></button>
    </div>
</div>
            <div class="rastreamento-modal-body">
                <div class="rastreamento-table-container">
                    <table class="rastreamento-table table-striped">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Tipo</th>
                                <th>Título</th>
                                <th>URL Destino</th>
                                <th>IP</th>
                                <th>Dispositivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimos_cliques)): foreach ($ultimos_cliques as $clique): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($clique['data_clique'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                    echo match ($clique['tipo']) {
                                                        'galeria-home' => 'primary',
                                                        'galeria-servicos' => 'success',
                                                        'apoiador' => 'info',
                                                        'pagina-servicos' => 'warning',
                                                        'servico' => 'danger',
                                                        'galeria' => 'secondary',
                                                        default => 'light'
                                                    };
                                                    ?>">
                                                <?php echo $clique['tipo_formatado']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($clique['titulo']); ?></td>
                                        <td>
                                            <?php if (!empty($clique['url_destino'])): ?>
                                                <a href="<?php echo htmlspecialchars($clique['url_destino']); ?>"
                                                    target="_blank"
                                                    title="<?php echo htmlspecialchars($clique['url_destino']); ?>">
                                                    <?php echo htmlspecialchars(substr($clique['url_destino'], 0, 30)) . '...'; ?>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($clique['ip_visitante']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($clique['dispositivo'], 0, 30)); ?></td>
                                    </tr>
                            <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    function abrirUltimosCliques() {
        const modal = document.getElementById('modalUltimosCliques');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function exportarDados() {
    // Pegar dados da tabela
    const table = document.querySelector('.rastreamento-table');
    let csv = [];

    // Cabeçalhos
    const headers = [];
    table.querySelectorAll('th').forEach(th => {
        headers.push(th.textContent.trim());
    });
    csv.push(headers.join(';'));

    // Dados das linhas
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(cell => {
            // Remove HTML tags e usa apenas o texto
            let text = cell.textContent.trim().replace(/"/g, '""');
            rowData.push(`"${text}"`);
        });
        csv.push(rowData.join(';'));
    });

    // Criar arquivo CSV
    const csvContent = csv.join('\n');
    const blob = new Blob(['\ufeff' + csvContent], {
        type: 'text/csv;charset=utf-8;'
    });

    // Download do arquivo
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'rastreamento_cliques.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

    function fecharModal() {
        const modal = document.getElementById('modalUltimosCliques');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        const modal = document.getElementById('modalUltimosCliques');
        if (event.target == modal) {
            fecharModal();
        }
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            fecharModal();
        }
    });
</script>

<?php require_once $root_path . 'includes/footer.php'; ?>
