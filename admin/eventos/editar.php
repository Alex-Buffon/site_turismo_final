<?php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if (!$evento) {
    header('Location: index.php');
    exit;
}
?>

<div class="content-header">
    <h2>Editar Evento</h2>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<div class="content-body">
    <form method="POST" action="processar_evento.php" enctype="multipart/form-data" class="form-padrao">
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($evento['titulo']); ?>"
                required class="form-control">
        </div>

        <div class="form-group">
            <label>Local:</label>
            <input type="text" name="local" value="<?php echo htmlspecialchars($evento['local']); ?>"
                required class="form-control">
        </div>

        <div class="form-group">
            <label>Data e Hora de Início:</label>
            <input type="datetime-local" name="data_inicio"
                value="<?php echo date('Y-m-d\TH:i', strtotime($evento['data_inicio'])); ?>"
                required class="form-control">
        </div>

        <div class="form-group">
            <label>Data e Hora de Término:</label>
            <input type="datetime-local" name="data_fim"
                value="<?php echo date('Y-m-d\TH:i', strtotime($evento['data_fim'])); ?>"
                required class="form-control">
        </div>

        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4" class="form-control"><?php
                                                                        echo htmlspecialchars($evento['descricao']);
                                                                        ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagem Atual:</label>
            <?php if ($evento['imagem']): ?>
                <img src="<?php echo BASE_URL; ?>/uploads/eventos/<?php echo $evento['imagem']; ?>"
                    style="max-width: 200px; display: block; margin: 10px 0;">
            <?php endif; ?>
            <label>Nova Imagem (opcional):</label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status" required class="form-control">
                <option value="ativo" <?php echo $evento['status'] == 'ativo' ? 'selected' : ''; ?>>
                    Ativo
                </option>
                <option value="inativo" <?php echo $evento['status'] == 'inativo' ? 'selected' : ''; ?>>
                    Inativo
                </option>
            </select>
        </div>

        <input type="hidden" name="id" value="<?php echo $evento['id']; ?>">
        <input type="hidden" name="acao" value="editar">
        <button type="submit" class="btn btn-primary">Atualizar Evento</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
