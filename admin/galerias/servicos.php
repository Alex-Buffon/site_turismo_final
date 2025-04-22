<!-- Modal de Edição -->
<div class="modal" id="editarModal">
    <div class="modal-content">
        <h3>Editar Imagem</h3>
        <form method="POST" enctype="multipart/form-data" id="formEditarImagem">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" id="edit_titulo" required>
            </div>

            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" id="edit_descricao" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Imagem Atual:</label>
                <img id="imagem_atual" src="" style="max-width: 200px; margin: 10px 0;">
                <br>
                <label>Nova Imagem (opcional):</label>
                <input type="file" name="imagem" accept="image/*">
            </div>

            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="tipo" value="servicos">

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Galeria de Serviços</h2>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addImagem">
        Adicionar Imagem
    </button>
</div>

<div class="content-body">
    <div class="galeria-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM galerias WHERE tipo = 'servicos' ORDER BY id DESC");
        while ($imagem = $stmt->fetch()) {
        ?>
            <div class="galeria-item">
                <img src="<?php echo BASE_URL; ?>/uploads/galeria/<?php echo $imagem['imagem']; ?>"
                     alt="<?php echo $imagem['titulo']; ?>">
                <div class="galeria-info">
                    <h4><?php echo $imagem['titulo']; ?></h4>
                    <p><?php echo $imagem['descricao']; ?></p>
                    <div class="galeria-acoes">
                        <button class="btn btn-sm btn-info" onclick="editarImagem(<?php echo $imagem['id']; ?>)">
                            Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="excluirImagem(<?php echo $imagem['id']; ?>)">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal Adicionar -->
<div class="modal" id="addImagem">
    <div class="modal-content">
        <h3>Adicionar Imagem</h3>
        <form method="POST" enctype="multipart/form-data" action="processar_galeria.php">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Imagem:</label>
                <input type="file" name="imagem" accept="image/*" required>
            </div>
            <input type="hidden" name="tipo" value="servicos">
            <input type="hidden" name="acao" value="adicionar">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
