<?php
require_once '../includes/conexao.php';
session_start();

if (!isset($_SESSION['admin_logado'])) {
    http_response_code(403);
    die('Acesso negado');
}

$response = ['sucesso' => false, 'mensagem' => ''];

// Buscar imagem para edição
if (isset($_GET['acao']) && $_GET['acao'] == 'buscar') {
    try {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) throw new Exception("ID inválido");

        $stmt = $pdo->prepare("SELECT * FROM galerias WHERE id = ?");
        $stmt->execute([$id]);
        $imagem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($imagem) {
            $response = [
                'sucesso' => true,
                'imagem' => [
                    'id' => $imagem['id'],
                    'titulo' => $imagem['titulo'],
                    'descricao' => $imagem['descricao'],
                    'url' => $imagem['url'],
                    'tipo' => $imagem['tipo'],
                    'ordem' => $imagem['ordem'],
                    'imagem' => $imagem['imagem']
                ]
            ];
        } else {
            throw new Exception("Imagem não encontrada");
        }
    } catch (Exception $e) {
        $response['mensagem'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Processar edição/adição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validação e sanitização
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo = htmlspecialchars(trim($_POST['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
        $descricao = htmlspecialchars(trim($_POST['descricao'] ?? ''), ENT_QUOTES, 'UTF-8');
        $tipo = in_array($_POST['tipo'] ?? '', ['home', 'servicos']) ? $_POST['tipo'] : 'home';
        $url = filter_var($_POST['url'] ?? '', FILTER_SANITIZE_URL);
        $ordem = filter_input(INPUT_POST, 'ordem', FILTER_VALIDATE_INT) ?: 0;

        if (empty($titulo)) {
            throw new Exception("O título é obrigatório");
        }

        // Upload de imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $permitidos)) {
                throw new Exception("Tipo de arquivo não permitido");
            }

            $novo_nome = uniqid() . '.' . $ext;
            $dir = "../uploads/galerias/";

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
                // Remove imagem antiga se estiver editando
                if ($id) {
                    $stmt = $pdo->prepare("SELECT imagem FROM galerias WHERE id = ?");
                    $stmt->execute([$id]);
                    $imagem_antiga = $stmt->fetchColumn();

                    if ($imagem_antiga && file_exists($dir . $imagem_antiga)) {
                        unlink($dir . $imagem_antiga);
                    }
                }
            } else {
                throw new Exception("Erro ao fazer upload da imagem");
            }
        }

        // Preparação do SQL
        if ($id) {
            $sql = "UPDATE galerias SET
                    titulo = ?,
                    descricao = ?,
                    tipo = ?,
                    ordem = ?,
                    url = ?";

            $params = [$titulo, $descricao, $tipo, $ordem, $url];

            if (isset($novo_nome)) {
                $sql .= ", imagem = ?";
                $params[] = $novo_nome;
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;
        } else {
            if (!isset($novo_nome)) {
                throw new Exception("É necessário enviar uma imagem");
            }

            $sql = "INSERT INTO galerias (titulo, descricao, tipo, ordem, imagem, url)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$titulo, $descricao, $tipo, $ordem, $novo_nome, $url];
        }

        // Executa a query
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $response['sucesso'] = true;
            $response['mensagem'] = $id ? 'Imagem atualizada com sucesso!' : 'Imagem adicionada com sucesso!';
        } else {
            throw new Exception("Erro ao salvar no banco de dados");
        }
    } catch (Exception $e) {
        $response['mensagem'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Exclusão de imagem
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    try {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) throw new Exception("ID inválido");

        // Busca a imagem antes de excluir
        $stmt = $pdo->prepare("SELECT imagem FROM galerias WHERE id = ?");
        $stmt->execute([$id]);
        $imagem = $stmt->fetchColumn();

        // Exclui do banco
        $stmt = $pdo->prepare("DELETE FROM galerias WHERE id = ?");
        if ($stmt->execute([$id])) {
            // Remove o arquivo físico
            if ($imagem) {
                $arquivo = "../uploads/galerias/" . $imagem;
                if (file_exists($arquivo)) {
                    unlink($arquivo);
                }
            }

            $response = [
                'sucesso' => true,
                'mensagem' => 'Imagem excluída com sucesso!'
            ];
        } else {
            throw new Exception("Erro ao excluir imagem");
        }
    } catch (Exception $e) {
        $response = [
            'sucesso' => false,
            'mensagem' => $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
