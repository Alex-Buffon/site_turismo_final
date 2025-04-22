<?php
require_once '../includes/conexao.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['admin_logado'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Captura dados do formulário
        $titulo = trim($_POST['titulo'] ?? '');
        $tipo = $_POST['tipo'] ?? '';
        $local = trim($_POST['local'] ?? '');
        $data_inicio = $_POST['data_inicio'] ?? '';
        $status = $_POST['status'] ?? 'ativo';
        $url = trim($_POST['url'] ?? '');

        // Validação dos campos obrigatórios
        if(empty($titulo) || empty($tipo) || empty($local) || empty($data_inicio)) {
            throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
        }

        // Processamento da imagem
        $imagem = null;
        if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            $imagem = uniqid() . '.' . $ext;
            $diretorio = '../uploads/eventos/';

            // Cria o diretório se não existir
            if(!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            // Move o arquivo
            if(!move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio . $imagem)) {
                throw new Exception('Erro ao salvar imagem');
            }
        }

        // Inserção ou atualização no banco
        if(empty($_POST['id'])) {
            // Novo evento
            $sql = "INSERT INTO eventos (titulo, tipo, local, data_inicio, status, url, imagem)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$titulo, $tipo, $local, $data_inicio, $status, $url, $imagem];
            $mensagem = 'Evento adicionado com sucesso!';
        } else {
            // Atualização de evento
            if($imagem) {
                // Remove imagem antiga se existir
                $stmt = $pdo->prepare("SELECT imagem FROM eventos WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $img_antiga = $stmt->fetchColumn();

                if($img_antiga && file_exists("../uploads/eventos/{$img_antiga}")) {
                    unlink("../uploads/eventos/{$img_antiga}");
                }

                $sql = "UPDATE eventos SET titulo = ?, tipo = ?, local = ?, data_inicio = ?,
                        status = ?, url = ?, imagem = ? WHERE id = ?";
                $params = [$titulo, $tipo, $local, $data_inicio, $status, $url, $imagem, $_POST['id']];
            } else {
                $sql = "UPDATE eventos SET titulo = ?, tipo = ?, local = ?, data_inicio = ?,
                        status = ?, url = ? WHERE id = ?";
                $params = [$titulo, $tipo, $local, $data_inicio, $status, $url, $_POST['id']];
            }
            $mensagem = 'Evento atualizado com sucesso!';
        }

        // Executa a query
        $stmt = $pdo->prepare($sql);
        $sucesso = $stmt->execute($params);

        if($sucesso) {
            echo json_encode([
                'sucesso' => true,
                'mensagem' => $mensagem,
                'id' => empty($_POST['id']) ? $pdo->lastInsertId() : $_POST['id']
            ]);
        } else {
            throw new Exception('Erro ao salvar o evento');
        }

    } catch(Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
    }
}
