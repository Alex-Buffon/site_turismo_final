<?php
require_once '../includes/header.php';

$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'hotel';
$tipos = [
    'hotel' => 'Hotel',
    'pousada' => 'Pousadas',
    'restaurante' => 'Restaurantes',
    'lanchonete' => 'Lanchonetes',
    'passeio' => 'Passeios'
];
?>

<div class="content-header">
    <h2><?php echo $tipos[$tipo]; ?></h2>
    <a href="adicionar.php?tipo=<?php echo $tipo; ?>" class="btn btn-primary">
        Adicionar <?php echo rtrim($tipos[$tipo], 's'); ?>
    </a>
</div>

<div class="content-body">
    <!-- Navegação entre tipos -->
    <div class="tipos-nav">
        <?php foreach($tipos as $key => $nome): ?>
            <a href="?tipo=<?php echo $key; ?>"
               class="btn <?php echo $tipo === $key ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo $nome; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Mensagem de feedback -->
    <?php if(isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success">
            <?php
            echo $_SESSION['mensagem'];
            unset($_SESSION['mensagem']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Lista de serviços -->
    <div class="servicos-grid">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE tipo = ? ORDER BY id DESC");
        $stmt->execute([$tipo]);
        while ($servico = $stmt->fetch()):
        ?>
            <div class="servico-item">
                <?php if($servico['imagem']): ?>
                    <img src="<?php echo BASE_URL; ?>/uploads/servicos/<?php echo $servico['imagem']; ?>"
                         alt="<?php echo htmlspecialchars($servico['nome']); ?>">
                <?php endif; ?>

                <div class="servico-info">
                    <h4><?php echo htmlspecialchars($servico['nome']); ?></h4>
                    <p class="endereco"><?php echo htmlspecialchars($servico['endereco']); ?></p>
                    <p class="telefone"><?php echo htmlspecialchars($servico['telefone']); ?></p>
                    <p class="descricao"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                    <div class="servico-acoes">
                        <a href="editar.php?id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                        <a href="javascript:void(0)"
                           onclick="excluirServico(<?php echo $servico['id']; ?>)"
                           class="btn btn-sm btn-danger">Excluir</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function excluirServico(id) {
    if(confirm('Deseja realmente excluir este serviço?')) {
        window.location.href = `excluir.php?id=${id}`;
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
