<?php
require_once 'includes/header.php';
?>

<div class="content-header">
    <h1>Dashboard</h1>
</div>

<div class="content-body">
    <!-- Cards do Dashboard -->
    <div class="dashboard-cards">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="card-info">
                <h3>Contatos</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM contatos WHERE status = 'não lido'");
                $novosContatos = $stmt->fetchColumn();
                ?>
                <p><?php echo $novosContatos; ?> não lidos</p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="card-info">
                <h3>Comentários</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM comentarios WHERE status = 'pendente'");
                $comentariosPendentes = $stmt->fetchColumn();
                ?>
                <p><?php echo $comentariosPendentes; ?> pendentes</p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="card-info">
                <h3>Eventos</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM eventos WHERE status = 'ativo'");
                $eventosAtivos = $stmt->fetchColumn();
                ?>
                <p><?php echo $eventosAtivos; ?> ativos</p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-hotel"></i>
            </div>
            <div class="card-info">
                <h3>Serviços</h3>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM servicos");
                $totalServicos = $stmt->fetchColumn();
                ?>
                <p><?php echo $totalServicos; ?> cadastrados</p>
            </div>
        </div>
    </div>

    <!-- Eventos Recentes -->
    <div class="recent-section">
        <h2>Eventos Recentes</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Local</th>
                        <th>Data Início</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM eventos ORDER BY data_inicio DESC LIMIT 5");
                        while($evento = $stmt->fetch()):
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($evento['local']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($evento['data_inicio'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $evento['status'] == 'ativo' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($evento['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="eventos/editar.php?id=<?php echo $evento['id']; ?>"
                                   class="btn btn-sm btn-info">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='5' class='text-center text-danger'>Erro ao carregar eventos</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
