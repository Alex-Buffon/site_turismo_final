<?php
require_once '../includes/header.php';

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM apoiadores WHERE id = ?");
$stmt->execute([$id]);
$apoiador = $stmt->fetch();

if(!$apoiador) {
    header('Location: index.php');
    exit;
}

if(isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $site = $_POST['site'];
    $imagem_atual = $apoiador['imagem'];

    // Upload de nova imagem
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $ext;
        $dir = "../uploads/apoiadores/";

        if(move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
            // Apaga imagem antiga
            if($imagem_atual && file_exists($dir . $imagem_atual)) {
                unlink($dir . $imagem_atual);
            }
            $imagem_atual = $novo_nome;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE apoiadores SET nome = ?, site = ?, imagem = ? WHERE id = ?");
        $stmt->execute([$nome, $site, $imagem_atual, $id]);
        header('Location: index.php?msg=atualizado');
        exit;
    } catch(PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>

<div class="content-header">
    <h2>Editar Apoiador</h2>
    <a href="index.php" class="btn btn-primary">Voltar</a>
</div>

<div class="content-body">
    <?php if(isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($apoiador['nome']); ?>" required>
        </div>

        <div class="form-group">
            <label>Site:</label>
            <input type="url" name="site" value="<?php echo htmlspecialchars($apoiador['site']); ?>">
        </div>

        <div class="form-group">
            <label>Logo/Imagem:</label>
            <?php if($apoiador['imagem']): ?>
                <div class="imagem-atual">
                    <img src="../uploads/apoiadores/<?php echo $apoiador['imagem']; ?>" width="100">
                </div>
            <?php endif; ?>
            <input type="file" name="imagem" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
