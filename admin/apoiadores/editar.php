<?php
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $nome = trim($_POST['nome']);
    $posicao = $_POST['posicao'];
    $ordem = (int)$_POST['ordem'];
    $site = trim($_POST['site']);

    try {
        if (!empty($_FILES['imagem']['name'])) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $novo_nome = uniqid() . '.' . $ext;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], "../uploads/apoiadores/$novo_nome")) {
                // Remove imagem antiga
                $stmt = $pdo->prepare("SELECT imagem FROM apoiadores WHERE id = ?");
                $stmt->execute([$id]);
                $imagem_antiga = $stmt->fetchColumn();
                if ($imagem_antiga) {
                    @unlink("../uploads/apoiadores/$imagem_antiga");
                }

                $stmt = $pdo->prepare("UPDATE apoiadores SET nome = ?, posicao = ?, ordem = ?, site = ?, imagem = ? WHERE id = ?");
                $stmt->execute([$nome, $posicao, $ordem, $site, $novo_nome, $id]);
            } else {
                $erro = 'Erro ao fazer upload da imagem';
            }
        } else {
            $stmt = $pdo->prepare("UPDATE apoiadores SET nome = ?, posicao = ?, ordem = ?, site = ? WHERE id = ?");
            $stmt->execute([$nome, $posicao, $ordem, $site, $id]);
        }

        if (!isset($erro)) {
            header('Location: index.php?success=1');
            exit;
        }
    } catch (PDOException $e) {
        $erro = 'Erro ao salvar: ' . $e->getMessage();
    }
}

// Busca dados do apoiador
$stmt = $pdo->prepare("SELECT * FROM apoiadores WHERE id = ?");
$stmt->execute([$id]);
$apoiador = $stmt->fetch();

if (!$apoiador) {
    header('Location: index.php');
    exit;
}
?>

<div class="content-header">
    <h2>Editar Apoiador</h2>
</div>

<div class="content-body">
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form action="editar.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $apoiador['id']; ?>">

        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control"
                value="<?php echo htmlspecialchars($apoiador['nome']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Posição</label>
            <select name="posicao" class="form-select" required>
                <option value="esquerda" <?php echo $apoiador['posicao'] == 'esquerda' ? 'selected' : ''; ?>>
                    Esquerda
                </option>
                <option value="direita" <?php echo $apoiador['posicao'] == 'direita' ? 'selected' : ''; ?>>
                    Direita
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ordem</label>
            <input type="number" name="ordem" class="form-control"
                value="<?php echo $apoiador['ordem']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Site</label>
            <input type="url" name="site" class="form-control"
                value="<?php echo htmlspecialchars($apoiador['site']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Imagem</label>
            <?php if ($apoiador['imagem']): ?>
                <div class="mb-2">
                    <img src="../uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                        alt="Imagem atual" style="max-height: 100px;">
                </div>
            <?php endif; ?>
            <input type="file" name="imagem" class="form-control" accept="image/*">
            <small class="text-muted">Deixe em branco para manter a imagem atual</small>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
