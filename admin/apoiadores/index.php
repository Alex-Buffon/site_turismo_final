<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gerenciar Apoiadores</h2>
    <a href="adicionar.php" class="btn btn-primary">Adicionar Novo</a>
</div>

<div class="content-body">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Imagem</th>
                <th>Site</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM apoiadores ORDER BY id DESC");
            while ($apoiador = $stmt->fetch()) {
            ?>
            <tr>
                <td><?php echo $apoiador['id']; ?></td>
                <td><?php echo $apoiador['nome']; ?></td>
                <td>
                    <?php if($apoiador['imagem']): ?>
                        <img src="../uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                             width="50" alt="<?php echo $apoiador['nome']; ?>">
                    <?php endif; ?>
                </td>
                <td><?php echo $apoiador['site']; ?></td>
                <td>
                    <a href="editar.php?id=<?php echo $apoiador['id']; ?>"
                       class="btn btn-sm btn-info">Editar</a>
                    <a href="excluir.php?id=<?php echo $apoiador['id']; ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Confirma exclusão?')">Excluir</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
