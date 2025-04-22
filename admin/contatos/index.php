<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gerenciar Contatos</h2>
</div>

<div class="content-body">
    <?php if(isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success">
            <?php
            echo $_SESSION['mensagem'];
            unset($_SESSION['mensagem']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Mensagem</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM contatos ORDER BY id DESC");
                    while($contato = $stmt->fetch()):
                ?>
                    <tr data-contato="<?php echo $contato['id']; ?>">
                        <td><?php echo htmlspecialchars($contato['nome']); ?></td>
                        <td><?php echo htmlspecialchars($contato['email']); ?></td>
                        <td><?php echo htmlspecialchars($contato['telefone']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-link"
                                    onclick="verMensagem('<?php echo htmlspecialchars($contato['mensagem']); ?>', <?php echo $contato['id']; ?>)">
                                Ver mensagem
                            </button>
                        </td>
                        <td>
                            <?php
                            echo isset($contato['created_at'])
                                ? date('d/m/Y H:i', strtotime($contato['created_at']))
                                : '-';
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $contato['status'] == 'lido' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($contato['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($contato['status'] == 'não lido'): ?>
                                <button onclick="marcarComoLido(<?php echo $contato['id']; ?>)"
                                        class="btn btn-sm btn-success">
                                    Marcar como lido
                                </button>
                            <?php endif; ?>
                            <button onclick="excluirContato(<?php echo $contato['id']; ?>)"
                                    class="btn btn-sm btn-danger">
                                Excluir
                            </button>
                        </td>
                    </tr>
                <?php
                    endwhile;
                } catch(PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center text-danger'>Erro ao carregar contatos: " .
                         htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ver Mensagem -->
<div class="modal" id="modalMensagem">
    <div class="modal-content">
        <h3>Mensagem do Contato</h3>
        <div id="mensagemConteudo"></div>
        <button type="button" class="btn btn-secondary" onclick="fecharModal()">Fechar</button>
    </div>
</div>

<script>
function verMensagem(mensagem, id) {
    document.getElementById('mensagemConteudo').textContent = mensagem;
    document.getElementById('modalMensagem').style.display = 'block';

    // Marcar como lido automaticamente
    fetch(`marcar_lido.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.sucesso) {
                // Atualizar visual do status
                const row = document.querySelector(`[data-contato="${id}"]`);
                if(row) {
                    const statusBadge = row.querySelector('.badge');
                    statusBadge.className = 'badge badge-success';
                    statusBadge.textContent = 'Lido';

                    // Remover botão "Marcar como lido"
                    const btnLido = row.querySelector('.btn-success');
                    if(btnLido) {
                        btnLido.remove();
                    }
                }
            }
        });
}

function marcarComoLido(id) {
    fetch(`marcar_lido.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.sucesso) {
                const row = document.querySelector(`[data-contato="${id}"]`);
                if(row) {
                    const statusBadge = row.querySelector('.badge');
                    statusBadge.className = 'badge badge-success';
                    statusBadge.textContent = 'Lido';

                    const btnLido = row.querySelector('.btn-success');
                    if(btnLido) {
                        btnLido.remove();
                    }
                }
            } else {
                alert('Erro ao marcar como lido: ' + data.mensagem);
            }
        });
}

function excluirContato(id) {
    if(confirm('Tem certeza que deseja excluir este contato?')) {
        fetch(`excluir.php?id=${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na requisição');
                }
                return response.json();
            })
            .then(data => {
                if(data.sucesso) {
                    // Remove a linha da tabela
                    const row = document.querySelector(`tr[data-contato="${id}"]`);
                    if(row) {
                        row.remove();
                        mostrarNotificacao('Contato excluído com sucesso!', 'success');
                    }
                } else {
                    mostrarNotificacao(data.mensagem || 'Erro ao excluir contato', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao processar a requisição', 'error');
            });
    }
}

// Atualiza a função de notificação
function mostrarNotificacao(mensagem, tipo) {
    // Remove notificações anteriores
    const notificacoesAnteriores = document.querySelectorAll('.notification');
    notificacoesAnteriores.forEach(n => n.remove());

    // Cria nova notificação
    const div = document.createElement('div');
    div.className = `alert alert-${tipo} notification`;
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.zIndex = '9999';
    div.textContent = mensagem;

    document.body.appendChild(div);

    // Remove após 3 segundos
    setTimeout(() => {
        if(div && div.parentElement) {
            div.remove();
        }
    }, 3000);
}


function fecharModal() {
    document.getElementById('modalMensagem').style.display = 'none';
}
</script>

<?php require_once '../includes/footer.php'; ?>
