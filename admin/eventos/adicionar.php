<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Adicionar Evento</h2>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>

<div class="content-body">
    <form method="POST" action="processar_evento.php" enctype="multipart/form-data" class="form-padrao">
        <div class="form-group">
            <label>Título:</label>
            <input type="text" name="titulo" required class="form-control">
        </div>

        <div class="form-group">
            <label>Local:</label>
            <input type="text" name="local" required class="form-control">
        </div>

        <div class="form-group">
            <label>Data e Hora de Início:</label>
            <input type="datetime-local" name="data_inicio" required class="form-control">
        </div>

        <div class="form-group">
            <label>Data e Hora de Término:</label>
            <input type="datetime-local" name="data_fim" required class="form-control">
        </div>

        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label>Imagem do Evento:</label>
            <input type="file" name="imagem" accept="image/*" class="form-control">
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status" required class="form-control">
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>

        <input type="hidden" name="acao" value="adicionar">
        <button type="submit" class="btn btn-primary">Salvar Evento</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
