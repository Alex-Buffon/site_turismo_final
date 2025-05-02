<?php
require_once '../includes/header.php';

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $posicao = $_POST['posicao'];
    $ordem = (int)$_POST['ordem'];
    $site = trim($_POST['site']);

    try {
        if (!empty($_FILES['imagem']['name'])) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $novo_nome = uniqid() . '.' . $ext;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], "../uploads/apoiadores/$novo_nome")) {
                $stmt = $pdo->prepare("INSERT INTO apoiadores (nome, posicao, ordem, site, imagem) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $posicao, $ordem, $site, $novo_nome]);

                header('Location: index.php?success=1');
                exit;
            } else {
                $erro = 'Erro ao fazer upload da imagem';
            }
        } else {
            $erro = 'A imagem é obrigatória';
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}
?>

<div class="content-header">
    <h2>Adicionar Apoiador</h2>
</div>

<div class="content-body">
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form action="adicionar.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control"
                value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Posição</label>
            <select name="posicao" class="form-select" required>
                <option value="esquerda" <?php echo (isset($_POST['posicao']) && $_POST['posicao'] == 'esquerda') ? 'selected' : ''; ?>>
                    Esquerda
                </option>
                <option value="direita" <?php echo (isset($_POST['posicao']) && $_POST['posicao'] == 'direita') ? 'selected' : ''; ?>>
                    Direita
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ordem</label>
            <input type="number" name="ordem" class="form-control"
                value="<?php echo isset($_POST['ordem']) ? (int)$_POST['ordem'] : 0; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Site</label>
            <input type="url" name="site" class="form-control"
                value="<?php echo isset($_POST['site']) ? htmlspecialchars($_POST['site']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem</label>
            <input type="file" name="imagem" class="form-control" accept="image/*" required>
            <small class="text-muted">Formatos aceitos: JPG, PNG, GIF</small>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
