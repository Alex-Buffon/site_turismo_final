<?php
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];
    $url = $_POST['url'] ?? null; // Adicionada a variável url

    $imagem = '';

    // Upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $ext;
        $dir = "../uploads/servicos/";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
            $imagem = $novo_nome;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO servicos (tipo, nome, endereco, telefone, descricao, imagem, url)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$tipo, $nome, $endereco, $telefone, $descricao, $imagem, $url])) {
            $_SESSION['mensagem'] = 'Serviço adicionado com sucesso!';
            header('Location: index.php?tipo=' . $tipo);
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao cadastrar: " . $e->getMessage();
    }
}

$tipo = $_GET['tipo'] ?? 'hotel';
$tipos = [
    'hotel' => 'Hotel',
    'pousada' => 'Pousadas',
    'restaurante' => 'Restaurantes',
    'lanchonete' => 'Lanchonetes',
    'passeio' => 'Passeios'
];
?>

<div class="content-header">
    <h2>Adicionar <?php echo rtrim($tipos[$tipo], 's'); ?></h2>
    <a href="index.php?tipo=<?php echo $tipo; ?>" class="btn btn-secondary">Voltar</a>
</div>

<div class="content-body">
    <?php if (isset($erro)): ?>
        <div class="alert alert-erro"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-padrao">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" required class="form-control">
        </div>

        <div class="form-group">
            <label>Endereço:</label>
            <input type="text" name="endereco" required class="form-control">
        </div>

        <div class="form-group">
            <label>Telefone:</label>
            <input type="text" name="telefone" required class="form-control">
        </div>

        <div class="form-group">
            <label>URL:</label>
            <input type="url" name="url" class="form-control" placeholder="https://www.exemplo.com.br">
        </div>

        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Imagem:</label>
            <input type="file" name="imagem" accept="image/*" required class="form-control">
        </div>

        <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">

        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
