<?php
require_once '../includes/header.php';

if(isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $site = $_POST['site'];
    $imagem = '';

    // Upload da imagem
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $ext;
        $dir = "../uploads/apoiadores/";

        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if(move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
            $imagem = $novo_nome;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO apoiadores (nome, site, imagem) VALUES (?, ?, ?)");
        $stmt->execute([$nome, $site, $imagem]);
        header('Location: index.php?msg=sucesso');
        exit;
    } catch(PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}
?>

<div class="content-header">
    <h2>Adicionar Apoiador</h2>
    <a href="index.php" class="btn btn-primary">Voltar</a>
</div>

<div class="content-body">
    <?php if(isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" required>
        </div>

        <div class="form-group">
            <label>Site:</label>
            <input type="url" name="site">
        </div>

        <div class="form-group">
            <label>Logo/Imagem:</label>
            <input type="file" name="imagem" accept="image/*">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
