<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gerenciar Eventos</h2>
    <button type="button" class="btn btn-primary" onclick="abrirModal()">
        <i class="fas fa-plus"></i> Adicionar Evento
    </button>
</div>

<div class="content-body">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Local</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM eventos ORDER BY data_inicio DESC");
                    while ($evento = $stmt->fetch()):
                ?>
                        <tr data-id="<?php echo $evento['id']; ?>">
                            <td>
                                <?php if ($evento['imagem']): ?>
                                    <img src="../uploads/eventos/<?php echo $evento['imagem']; ?>"
                                        alt="Imagem do evento"
                                        class="evento-imagem-thumb"
                                        onclick="visualizarImagem(this.src)"
                                        title="Clique para ampliar">
                                <?php else: ?>
                                    <span class="sem-imagem">Sem imagem</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                            <td>
                                <span class="badge tipo-<?php echo $evento['tipo']; ?>">
                                    <?php echo ucfirst($evento['tipo']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($evento['local']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($evento['data_inicio'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $evento['status'] == 'ativo' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($evento['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="editarEvento(<?php echo $evento['id']; ?>)"
                                    class="btn-acao btn-editar">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button onclick="excluirEvento(<?php echo $evento['id']; ?>)"
                                    class="btn-acao btn-excluir">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </td>
                        </tr>
                <?php
                    endwhile;
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='text-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal do Evento -->
<div id="eventoModal" class="modal">
    <div class="modal-content">
        <h3>Adicionar Evento</h3>
        <form id="formEvento" onsubmit="salvarEvento(event)" enctype="multipart/form-data">
            <div class="form-group">
                <label>Título:</label>
                <input type="text" name="titulo" required class="form-control">
            </div>

            <div class="form-group">
                <label>Tipo:</label>
                <select name="tipo" required class="form-control">
                    <option value="">Selecione...</option>
                    <option value="gastronomico">Gastronômico</option>
                    <option value="esportivo">Esportivo</option>
                    <option value="religioso">Religioso</option>
                    <option value="festivo">Festivo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Local:</label>
                <input type="text" name="local" required class="form-control">
            </div>

            <div class="form-group">
                <label>Data:</label>
                <input type="date" name="data_inicio" required class="form-control">
            </div>

            <div class="form-group">
                <label>URL (opcional):</label>
                <input type="url" name="url" class="form-control" placeholder="http://">
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status" required class="form-control">
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Imagem:</label>
                <input type="file" name="imagem" class="form-control">
                <small class="text-muted">Envie qualquer tipo de imagem</small>
                <div id="preview-imagem"></div>
            </div>

            <input type="hidden" name="id" id="evento_id">

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Funções do Modal
    function abrirModal() {
        document.getElementById('formEvento').reset();
        document.getElementById('evento_id').value = '';
        document.querySelector('#eventoModal h3').textContent = 'Adicionar Evento';
        document.getElementById('eventoModal').style.display = 'block';
        document.getElementById('preview-imagem').innerHTML = '';
    }

    function fecharModal() {
        document.getElementById('eventoModal').style.display = 'none';
    }

    // Função para editar evento
    function editarEvento(id) {
        fetch(`buscar_evento.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.querySelector('[name="titulo"]').value = data.titulo;
                    document.querySelector('[name="tipo"]').value = data.tipo;
                    document.querySelector('[name="local"]').value = data.local;
                    document.querySelector('[name="data_inicio"]').value = data.data_inicio;
                    document.querySelector('[name="status"]').value = data.status;
                    document.querySelector('[name="url"]').value = data.url || '';
                    document.getElementById('evento_id').value = data.id;

                    const previewDiv = document.getElementById('preview-imagem');
                    if (data.imagem) {
                        previewDiv.innerHTML = `
                        <div class="preview-wrapper">
                            <img src="../uploads/eventos/${data.imagem}" alt="Preview">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removerImagem()">
                                <i class="fas fa-times"></i> Remover
                            </button>
                        </div>
                    `;
                    } else {
                        previewDiv.innerHTML = '';
                    }

                    document.querySelector('#eventoModal h3').textContent = 'Editar Evento';
                    document.getElementById('eventoModal').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao carregar evento', 'error');
            });
    }

    // Função para excluir evento
    function excluirEvento(id) {
        if (confirm('Tem certeza que deseja excluir este evento?')) {
            fetch(`excluir_evento.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.sucesso) {
                        const elemento = document.querySelector(`tr[data-id="${id}"]`);
                        if (elemento) {
                            elemento.remove();
                            mostrarNotificacao('Evento excluído com sucesso!', 'success');
                        }
                    } else {
                        mostrarNotificacao(data.mensagem || 'Erro ao excluir evento', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    mostrarNotificacao('Erro ao excluir evento', 'error');
                });
        }
    }

    // Função para salvar evento
    function salvarEvento(e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        fetch('processar_eventos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    mostrarNotificacao(data.mensagem, 'success');
                    fecharModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacao(data.mensagem || 'Erro ao salvar evento', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao processar requisição', 'error');
            });
    }

    // Funções auxiliares
    function removerImagem() {
        document.querySelector('input[name="imagem"]').value = '';
        document.getElementById('preview-imagem').innerHTML = '';
    }

    function visualizarImagem(src) {
        const modal = document.createElement('div');
        modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        cursor: pointer;
    `;

        const img = document.createElement('img');
        img.src = src;
        img.style.cssText = `
        max-width: 90%;
        max-height: 90vh;
        border-radius: 4px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    `;

        modal.appendChild(img);
        document.body.appendChild(modal);
        modal.onclick = () => modal.remove();
    }

    // Preview da imagem
    document.querySelector('input[name="imagem"]').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-imagem').innerHTML = `
                <div class="preview-wrapper">
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removerImagem()">
                        <i class="fas fa-times"></i> Remover
                    </button>
                </div>
            `;
            };
            reader.readAsDataURL(file);
        }
    });

    function mostrarNotificacao(mensagem, tipo) {
        const notificacao = document.createElement('div');
        notificacao.className = `alert alert-${tipo}`;
        notificacao.textContent = mensagem;
        notificacao.style.position = 'fixed';
        notificacao.style.top = '20px';
        notificacao.style.right = '20px';
        notificacao.style.zIndex = '9999';
        notificacao.style.padding = '15px';
        notificacao.style.borderRadius = '4px';
        notificacao.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';

        document.body.appendChild(notificacao);
        setTimeout(() => notificacao.remove(), 3000);
    }
</script>

<?php require_once '../includes/footer.php'; ?>
