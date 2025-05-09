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

<div class="content-wrapper">
    <div class="content-header">
        <h2><i class="fas fa-concierge-bell"></i> Galeria de Serviços</h2>
        <button type="button" class="btn btn-primary" onclick="abrirModalAdicionar()">
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
                        <?php if (!empty($servico['url'])): ?>
                            <p class="text-muted">
                                <a href="<?php echo htmlspecialchars($servico['url']); ?>" target="_blank">
                                    <i class="fas fa-link"></i> Ver link
                                </a>
                            </p>
                        <?php endif; ?>
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
<div class="modal" id="servicoModal">
    <div class="modal-content">
        <h3>Gerenciar Serviço</h3>
        <form method="POST" enctype="multipart/form-data" id="formServico">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="tipo" value="servicos">
            <input type="hidden" name="acao" value="adicionar">

            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" id="edit_descricao" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>URL:</label>
                <input type="url" name="url" id="edit_url" class="form-control" placeholder="https://">
            </div>

            <div class="form-group">
                <label>Imagem:</label>
                <input type="file" name="imagem" class="form-control" accept="image/*">
                <div id="preview-container" class="mt-2" style="display:none">
                    <img id="imagem_preview" src="" style="max-width:200px">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form adicionar/editar
        const formServico = document.querySelector('#formServico');
        if (formServico) {
            formServico.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('processar_galeria.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        alert(data.mensagem);
                        location.reload();
                    } else {
                        alert(data.mensagem || 'Erro ao processar serviço');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar requisição');
                });
            });
        }
    });

    function editarServico(id) {
        fetch(`processar_galeria.php?acao=buscar&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('edit_id').value = data.imagem.id;
                    document.getElementById('edit_titulo').value = data.imagem.titulo;
                    document.getElementById('edit_descricao').value = data.imagem.descricao || '';
                    document.getElementById('edit_url').value = data.imagem.url || '';

                    const previewContainer = document.getElementById('preview-container');
                    const imagemPreview = document.getElementById('imagem_preview');

                    if (data.imagem.imagem) {
                        imagemPreview.src = '../uploads/galerias/' + data.imagem.imagem;
                        previewContainer.style.display = 'block';
                    }

                    document.getElementById('servicoModal').style.display = 'block';
                } else {
                    alert('Erro ao carregar serviço');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar serviço');
            });
    }




    // Adicione esta função no início do bloco script
    function abrirModalAdicionar() {
        const modal = document.getElementById('servicoModal');
        const form = document.getElementById('formServico');

        // Limpa o formulário
        form.reset();

        // Reseta os campos hidden
        form.querySelector('[name="id"]').value = '';
        form.querySelector('[name="acao"]').value = 'adicionar';

        // Esconde o preview da imagem
        document.getElementById('preview-container').style.display = 'none';

        // Mostra o modal
        modal.style.display = 'block';
    }


    

    function excluirServico(id) {
        if (confirm('Tem certeza que deseja excluir este serviço?')) {
            fetch(`processar_galeria.php?acao=excluir&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        location.reload();
                    } else {
                        alert(data.mensagem || 'Erro ao excluir serviço');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir serviço');
                });
        }
    }

    function fecharModal() {
        document.getElementById('servicoModal').style.display = 'none';
        document.getElementById('formServico').reset();
        document.getElementById('preview-container').style.display = 'none';
    }

    // Preview da imagem
    document.querySelector('input[type="file"]').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('preview-container');
        const imagemPreview = document.getElementById('imagem_preview');

        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagemPreview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(e.target.files[0]);
        } else {
            previewContainer.style.display = 'none';
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


