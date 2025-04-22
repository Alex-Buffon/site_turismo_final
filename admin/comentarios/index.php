<?php
require_once '../includes/header.php';
?>

<div class="content-header">
    <h2>Gerenciar Comentários</h2>
</div>

<div class="content-body">
    <!-- Filtros -->
    <div class="filtros mb-4">
        <select id="filtroStatus" class="form-control" onchange="filtrarComentarios()">
            <option value="">Todos os status</option>
            <option value="pendente">Pendentes</option>
            <option value="aprovado">Aprovados</option>
            <option value="reprovado">Reprovados</option>
        </select>
    </div>

    <!-- Lista de comentários -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Comentário</th>
                    <th>Avaliação</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="listaComentarios">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM comentarios ORDER BY created_at DESC");
                    while($comentario = $stmt->fetch()):
                ?>
                    <tr data-comentario="<?php echo $comentario['id']; ?>"
                        data-status="<?php echo $comentario['status']; ?>">
                        <td><?php echo htmlspecialchars($comentario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($comentario['email']); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-link"
                                    onclick="verComentario('<?php echo htmlspecialchars($comentario['comentario']); ?>')">
                                Ver comentário
                            </button>
                        </td>
                        <td>
                            <?php
                            for($i = 1; $i <= 5; $i++) {
                                echo $i <= $comentario['avaliacao'] ? '★' : '☆';
                            }
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php
                                echo $comentario['status'] == 'aprovado' ? 'success' :
                                    ($comentario['status'] == 'pendente' ? 'warning' : 'danger');
                            ?>">
                                <?php echo ucfirst($comentario['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($comentario['created_at'])); ?></td>
                        <td>
                            <?php if($comentario['status'] == 'pendente'): ?>
                                <button onclick="alterarStatus(<?php echo $comentario['id']; ?>, 'aprovado')"
                                        class="btn btn-sm btn-success">
                                    Aprovar
                                </button>
                                <button onclick="alterarStatus(<?php echo $comentario['id']; ?>, 'reprovado')"
                                        class="btn btn-sm btn-danger">
                                    Reprovar
                                </button>
                            <?php else: ?>
                                <button onclick="alterarStatus(<?php echo $comentario['id']; ?>, 'pendente')"
                                        class="btn btn-sm btn-warning">
                                    Voltar para pendente
                                </button>
                            <?php endif; ?>
                            <button onclick="excluirComentario(<?php echo $comentario['id']; ?>)"
                                    class="btn btn-sm btn-danger">
                                Excluir
                            </button>
                        </td>
                    </tr>
                <?php
                    endwhile;
                } catch(PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center text-danger'>Erro ao carregar comentários: " .
                         htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ver Comentário -->
<div class="modal" id="modalComentario">
    <div class="modal-content">
        <h3>Comentário Completo</h3>
        <div id="comentarioConteudo"></div>
        <button type="button" class="btn btn-secondary" onclick="fecharModal()">Fechar</button>
    </div>
</div>

<script>
function verComentario(comentario) {
    document.getElementById('comentarioConteudo').textContent = comentario;
    document.getElementById('modalComentario').style.display = 'block';
}

function alterarStatus(id, novoStatus) {
    fetch(`alterar_status.php?id=${id}&status=${novoStatus}`)
        .then(response => response.json())
        .then(data => {
            if(data.sucesso) {
                const row = document.querySelector(`tr[data-comentario="${id}"]`);
                if(row) {
                    location.reload();
                }
            } else {
                mostrarNotificacao(data.mensagem || 'Erro ao alterar status', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarNotificacao('Erro ao processar a requisição', 'error');
        });
}

function excluirComentario(id) {
    if(confirm('Tem certeza que deseja excluir este comentário?')) {
        fetch(`excluir.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if(data.sucesso) {
                    const row = document.querySelector(`tr[data-comentario="${id}"]`);
                    if(row) {
                        row.remove();
                        mostrarNotificacao('Comentário excluído com sucesso!', 'success');
                    }
                } else {
                    mostrarNotificacao(data.mensagem || 'Erro ao excluir comentário', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarNotificacao('Erro ao processar a requisição', 'error');
            });
    }
}

function filtrarComentarios() {
    const statusSelecionado = document.getElementById('filtroStatus').value;
    const linhas = document.querySelectorAll('#listaComentarios tr');
    let encontrados = 0;

    linhas.forEach(linha => {
        const status = linha.getAttribute('data-status');
        if (!statusSelecionado || status === statusSelecionado) {
            linha.style.display = '';
            encontrados++;
        } else {
            linha.style.display = 'none';
        }
    });

    const mensagemVazia = document.getElementById('mensagemVazia');
    if (encontrados === 0) {
        if (!mensagemVazia) {
            const msg = document.createElement('tr');
            msg.id = 'mensagemVazia';
            msg.innerHTML = `<td colspan="7" class="text-center">
                Nenhum comentário encontrado com o status "${statusSelecionado}"
            </td>`;
            document.querySelector('#listaComentarios').appendChild(msg);
        }
    } else if (mensagemVazia) {
        mensagemVazia.remove();
    }
}

function mostrarNotificacao(mensagem, tipo) {
    const notificacoesAnteriores = document.querySelectorAll('.notification');
    notificacoesAnteriores.forEach(n => n.remove());

    const div = document.createElement('div');
    div.className = `alert alert-${tipo} notification`;
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.zIndex = '9999';
    div.textContent = mensagem;

    document.body.appendChild(div);

    setTimeout(() => {
        if(div && div.parentElement) {
            div.remove();
        }
    }, 3000);
}

function fecharModal() {
    document.getElementById('modalComentario').style.display = 'none';
}
</script>

<?php require_once '../includes/footer.php'; ?>
