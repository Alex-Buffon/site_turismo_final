<?php
require_once '../includes/conexao.php';
require_once '../includes/header.php';

// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitização dos inputs
        $titulo = htmlspecialchars(trim($_POST['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
        $descricao = htmlspecialchars(trim($_POST['descricao'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tipo = $_POST['tipo'] === 'home' ? 'home' : 'servicos';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $url = filter_var($_POST['url'] ?? '', FILTER_SANITIZE_URL);
        $response = ['sucesso' => false, 'mensagem' => ''];

        if (empty($titulo)) {
            throw new Exception("O título é obrigatório");
        }

        // Processa upload da imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $imagem = $_FILES['imagem'];
            $ext = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));

            // Validação da extensão
            $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
            if (!in_array($ext, $permitidos)) {
                throw new Exception("Tipo de arquivo não permitido");
            }

            $novo_nome = uniqid() . '.' . $ext;
            $upload_dir = '../uploads/galerias/';

            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($imagem['tmp_name'], $upload_dir . $novo_nome)) {
                if ($id) {
                    $stmt = $pdo->prepare("SELECT imagem FROM galerias WHERE id = ?");
                    $stmt->execute([$id]);
                    $img_anterior = $stmt->fetchColumn();
                    if ($img_anterior && file_exists($upload_dir . $img_anterior)) {
                        unlink($upload_dir . $img_anterior);
                    }
                }
            } else {
                throw new Exception("Erro ao fazer upload da imagem");
            }
        }

        // Insere ou atualiza no banco
        if ($id) {
            $sql = "UPDATE galerias SET titulo = ?, descricao = ?, url = ?" .
                (isset($novo_nome) ? ", imagem = ?" : "") .
                " WHERE id = ?";
            $params = isset($novo_nome) ?
                [$titulo, $descricao, $url, $novo_nome, $id] :
                [$titulo, $descricao, $url, $id];
        } else {
            if (!isset($novo_nome)) {
                throw new Exception("É necessário enviar uma imagem");
            }
            $sql = "INSERT INTO galerias (titulo, descricao, imagem, tipo, url) VALUES (?, ?, ?, ?, ?)";
            $params = [$titulo, $descricao, $novo_nome, $tipo, $url];
        }

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $response['sucesso'] = true;
            $response['mensagem'] = 'Operação realizada com sucesso!';
        } else {
            throw new Exception("Erro ao salvar no banco de dados");
        }
    } catch (Exception $e) {
        $response['mensagem'] = $e->getMessage();
    }

    // Retorna JSON para requisições AJAX
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Busca imagens existentes
$stmt = $pdo->query("SELECT * FROM galerias WHERE tipo = 'home' ORDER BY ordem ASC, id DESC");
$imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-header">
    <h2>Galeria da Home</h2>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addImagem">
        Adicionar Imagem
    </button>
</div>

<div class="content-body">
    <div class="galeria-grid">
        <?php foreach ($imagens as $imagem): ?>
            <div class="galeria-item">
                <img src="../uploads/galerias/<?= htmlspecialchars($imagem['imagem']) ?>"
                    alt="<?= htmlspecialchars($imagem['titulo']) ?>">
                <div class="galeria-info">
                    <h4><?= htmlspecialchars($imagem['titulo']) ?></h4>
                    <p><?= htmlspecialchars($imagem['descricao']) ?></p>
                    <?php if (!empty($imagem['url'])): ?>
                        <p class="text-muted">
                            <a href="<?= htmlspecialchars($imagem['url']) ?>" target="_blank">
                                <i class="fas fa-link"></i> Ver link
                            </a>
                        </p>
                    <?php endif; ?>
                    <div class="galeria-acoes">
                        <button class="btn btn-sm btn-info" onclick="editarImagem(<?= $imagem['id'] ?>)">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="excluirImagem(<?= $imagem['id'] ?>)">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

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
                <label>URL:</label>
                <input type="url" name="url" id="edit_url" placeholder="https://">
            </div>

            <div class="form-group">
                <label>Imagem Atual:</label>
                <img id="imagem_atual" src="" style="max-width: 200px; margin: 10px 0;">
                <br>
                <label>Nova Imagem (opcional):</label>
                <input type="file" name="imagem" accept="image/*">
            </div>

            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="tipo" value="home">

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </div>
        </form>
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
                <label>URL:</label>
                <input type="url" name="url" placeholder="https://">
            </div>
            <div class="form-group">
                <label>Imagem:</label>
                <input type="file" name="imagem" accept="image/*" required>
            </div>
            <input type="hidden" name="tipo" value="home">
            <input type="hidden" name="acao" value="adicionar">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form adicionar
        const formAdd = document.querySelector('#addImagem form');
        if (formAdd) {
            formAdd.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // CORREÇÃO - Agora enviando para processar_galeria.php
                fetch('processar_galeria.php', { // <- LINHA CORRIGIDA
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            alert(data.mensagem);
                            location.reload();
                        } else {
                            alert(data.mensagem || 'Erro ao adicionar imagem');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao processar requisição');
                    });
            });
        }
    });

    function editarImagem(id) {
        fetch(`processar_galeria.php?acao=buscar&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('edit_id').value = data.imagem.id;
                    document.getElementById('edit_titulo').value = data.imagem.titulo;
                    document.getElementById('edit_descricao').value = data.imagem.descricao || '';
                    document.getElementById('edit_url').value = data.imagem.url || '';
                    document.getElementById('imagem_atual').src = '../uploads/galerias/' + data.imagem.imagem;
                    document.getElementById('editarModal').style.display = 'block';
                } else {
                    alert('Erro ao carregar imagem');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar imagem');
            });
    }

    function fecharModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }

    function excluirImagem(id) {
        if (confirm('Tem certeza que deseja excluir esta imagem?')) {
            fetch(`processar_galeria.php?acao=excluir&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        location.reload();
                    } else {
                        alert(data.mensagem || 'Erro ao excluir imagem');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao excluir imagem');
                });
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>
