<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexao.php';

// Verificar login
if (!isset($_SESSION['admin_logado'])) {
    header('Location: ../login.php');
    exit();
}

// Buscar serviços
try {
    $stmt = $pdo->prepare("SELECT * FROM galerias WHERE tipo = 'servicos' ORDER BY ordem ASC");
    $stmt->execute();
    $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao carregar serviços: " . $e->getMessage();
}
?>

<!-- Adicione estas dependências no head -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="content-wrapper">
    <div class="content-header">
        <h2><i class="fas fa-concierge-bell"></i> Galeria de Serviços</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#servicoModal">
            <i class="fas fa-plus"></i> Adicionar Serviço
        </button>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo']; ?> alert-dismissible fade show" role="alert">
            <?php
            echo $_SESSION['mensagem'];
            unset($_SESSION['mensagem']);
            unset($_SESSION['tipo']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="content-body">
        <div class="galeria-grid">
            <?php foreach ($servicos as $servico): ?>
                <div class="galeria-item">
                    <img src="../uploads/galerias/<?php echo htmlspecialchars($servico['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($servico['titulo']); ?>">
                    <div class="galeria-info">
                        <h4><?php echo htmlspecialchars($servico['titulo']); ?></h4>
                        <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
                        <div class="galeria-acoes">
                            <button class="btn btn-sm btn-info" onclick="editarServico(<?php echo $servico['id']; ?>)">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="excluirServico(<?php echo $servico['id']; ?>)">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="servicoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gerenciar Serviço</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formServico" enctype="multipart/form-data">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" name="tipo" value="servicos">
                    <input type="hidden" name="acao" value="adicionar">

                    <div class="form-group mb-3">
                        <label>Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Imagem</label>
                        <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                        <div id="preview-container" class="mt-2" style="display:none">
                            <img id="imagem_preview" src="" style="max-width:200px">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarServico()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function editarServico(id) {
        document.querySelector('#formServico [name="acao"]').value = 'editar';

        fetch(`processar_galeria.php?acao=buscar&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('id').value = data.imagem.id;
                    document.getElementById('titulo').value = data.imagem.titulo;
                    document.getElementById('descricao').value = data.imagem.descricao;
                    if (data.imagem.imagem) {
                        document.getElementById('preview-container').style.display = 'block';
                        document.getElementById('imagem_preview').src = '../uploads/galerias/' + data.imagem.imagem;
                    }
                    const modal = new bootstrap.Modal(document.getElementById('servicoModal'));
                    modal.show();
                } else {
                    alert(data.mensagem);
                }
            })
            .catch(error => console.error('Erro:', error));
    }

    function salvarServico() {
        const form = document.getElementById('formServico');
        const formData = new FormData(form);

        fetch('processar_galeria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('servicoModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert(data.mensagem);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar requisição');
            });
    }

    function excluirServico(id) {
        if (confirm('Tem certeza que deseja excluir este serviço?')) {
            fetch(`processar_galeria.php?acao=excluir&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        location.reload();
                    } else {
                        alert(data.mensagem);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir serviço');
                });
        }
    }

    // Preview da imagem
    document.getElementById('imagem').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-container').style.display = 'block';
                document.getElementById('imagem_preview').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Limpa o formulário quando o modal é fechado
    document.getElementById('servicoModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('formServico').reset();
        document.getElementById('preview-container').style.display = 'none';
        document.querySelector('#formServico [name="acao"]').value = 'adicionar';
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
