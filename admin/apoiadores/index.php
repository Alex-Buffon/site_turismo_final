<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gerenciar Apoiadores</h2>
    <a href="adicionar.php" class="btn btn-primary">Adicionar Novo</a>
</div>

<div class="content-body">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Operação realizada com sucesso!</div>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Imagem</th>
                <th>Posição</th>
                <th>Ordem</th>
                <th>Site</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT id, nome, imagem, posicao, ordem, COALESCE(site, '') as site FROM apoiadores ORDER BY posicao, ordem ASC");
            while ($apoiador = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
                <tr>
                    <td><?php echo $apoiador['id']; ?></td>
                    <td><?php echo htmlspecialchars($apoiador['nome']); ?></td>
                    <td>
                        <?php if ($apoiador['imagem']): ?>
                            <img src="../uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                                width="50" alt="<?php echo htmlspecialchars($apoiador['nome']); ?>">
                        <?php endif; ?>
                    </td>
                    <td><?php echo ucfirst($apoiador['posicao']); ?></td>
                    <td><?php echo $apoiador['ordem']; ?></td>
                    <td>
                        <?php if (!empty($apoiador['site'])): ?>
                            <a href="<?php echo htmlspecialchars($apoiador['site']); ?>"
                                target="_blank" class="btn btn-sm btn-link">
                                <i class="fas fa-external-link-alt"></i> Visitar
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="editar.php?id=<?php echo $apoiador['id']; ?>"
                            class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="excluir.php?id=<?php echo $apoiador['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Tem certeza que deseja excluir?')">
                            <i class="fas fa-trash"></i> Excluir
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
