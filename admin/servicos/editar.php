<?php
require_once '../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Buscar dados do serviço primeiro
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
$stmt->execute([$id]);
$servico = $stmt->fetch();

if (!$servico) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];
    $url = $_POST['url'] ?? null;

    try {
        // Preparar query base
        $campos = "nome = ?, endereco = ?, telefone = ?, descricao = ?, url = ?";
        $parametros = [$nome, $endereco, $telefone, $descricao, $url];

        // Se tem nova imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $ext;
            $dir = "../uploads/servicos/";

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
                // Apagar imagem antiga
                if ($servico['imagem'] && file_exists($dir . $servico['imagem'])) {
                    unlink($dir . $servico['imagem']);
                }

                // Adicionar imagem aos campos para atualização
                $campos .= ", imagem = ?";
                $parametros[] = $novo_nome;
            }
        }

        // Adicionar ID ao final dos parâmetros
        $parametros[] = $id;

        // Executar update
        $stmt = $pdo->prepare("UPDATE servicos SET {$campos} WHERE id = ?");
        $stmt->execute($parametros);

        $_SESSION['mensagem'] = 'Serviço atualizado com sucesso!';
        header('Location: index.php?tipo=' . $tipo);
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
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
    <?php if (isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-padrao">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" required class="form-control"
                value="<?php echo htmlspecialchars($servico['nome']); ?>">
        </div>

        <div class="form-group">
            <label>Endereço:</label>
            <input type="text" name="endereco" required class="form-control"
                value="<?php echo htmlspecialchars($servico['endereco']); ?>">
        </div>

        <div class="form-group">
            <label>Telefone:</label>
            <input type="text" name="telefone" required class="form-control"
                value="<?php echo htmlspecialchars($servico['telefone']); ?>">
        </div>

        <div class="form-group">
            <label>URL:</label>
            <input type="url" name="url" class="form-control"
                value="<?php echo htmlspecialchars($servico['url'] ?? ''); ?>"
                placeholder="https://www.exemplo.com.br">
        </div>

        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4" class="form-control"><?php
                                                                        echo htmlspecialchars($servico['descricao']);
                                                                        ?></textarea>
        </div>

        <div class="form-group">
            <label>Imagem:</label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
            <?php if ($servico['imagem']): ?>
                <p class="imagem-atual">Imagem atual: <?php echo $servico['imagem']; ?></p>
            <?php endif; ?>
        </div>

        <input type="hidden" name="tipo" value="<?php echo $servico['tipo']; ?>">

        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
