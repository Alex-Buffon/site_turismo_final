<?php
require_once '../includes/header.php';

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    try {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $ext;
            $dir = "../uploads/servicos/";

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
                // Buscar imagem antiga
                $stmt = $pdo->prepare("SELECT imagem FROM servicos WHERE id = ?");
                $stmt->execute([$id]);
                $imagem_antiga = $stmt->fetchColumn();

                // Apagar imagem antiga
                if ($imagem_antiga && file_exists($dir . $imagem_antiga)) {
                    unlink($dir . $imagem_antiga);
                }

                // Atualizar com nova imagem
                $stmt = $pdo->prepare("UPDATE servicos SET nome = ?, endereco = ?, telefone = ?,
                                     descricao = ?, imagem = ? WHERE id = ?");
                $stmt->execute([$nome, $endereco, $telefone, $descricao, $novo_nome, $id]);
            }
        } else {
            // Atualizar sem mudar a imagem
            $stmt = $pdo->prepare("UPDATE servicos SET nome = ?, endereco = ?, telefone = ?,
                                 descricao = ? WHERE id = ?");
            $stmt->execute([$nome, $endereco, $telefone, $descricao, $id]);
        }

        $_SESSION['mensagem'] = 'Serviço atualizado com sucesso!';
        header('Location: index.php?tipo=' . $tipo);
        exit;

    } catch(PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Buscar dados do serviço
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$servico = $stmt->fetch();

if(!$servico) {
    header('Location: index.php');
    exit;
}

$tipos = [
    'hotel' => 'Hotéis',
    'pousada' => 'Pousadas',
    'restaurante' => 'Restaurantes',
    'lanchonete' => 'Lanchonetes',
    'passeio' => 'Passeios'
];
?>

<div class="content-header">
    <h2>Editar <?php echo rtrim($tipos[$servico['tipo']], 's'); ?></h2>
    <a href="index.php?tipo=<?php echo $servico['tipo']; ?>" class="btn btn-secondary">Voltar</a>
</div>

<div class="content-body">
    <?php if(isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-padrao">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($servico['nome']); ?>"
                   required class="form-control">
        </div>

        <div class="form-group">
            <label>Endereço:</label>
            <input type="text" name="endereco" value="<?php echo htmlspecialchars($servico['endereco']); ?>"
                   required class="form-control">
        </div>

        <div class="form-group">
            <label>Telefone:</label>
            <input type="text" name="telefone" value="<?php echo htmlspecialchars($servico['telefone']); ?>"
                   required class="form-control">
        </div>

        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4" class="form-control"><?php echo htmlspecialchars($servico['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagem Atual:</label>
            <?php if($servico['imagem']): ?>
                <img src="<?php echo BASE_URL; ?>/uploads/servicos/<?php echo $servico['imagem']; ?>"
                     style="max-width: 200px; display: block; margin: 10px 0;">
            <?php endif; ?>
            <label>Nova Imagem (opcional):</label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
        </div>

        <input type="hidden" name="tipo" value="<?php echo $servico['tipo']; ?>">

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
