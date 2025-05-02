<?php
require_once '../includes/conexao.php';
session_start();

if (!isset($_SESSION['admin_logado'])) {
    die('Acesso negado');
}

$response = ['sucesso' => false, 'mensagem' => ''];

// Adicionar Serviço
if (isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $ext;
        $dir = "../uploads/servicos/";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO servicos (tipo, nome, endereco, telefone, descricao, imagem)
                                     VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$tipo, $nome, $endereco, $telefone, $descricao, $novo_nome])) {
                    $response['sucesso'] = true;
                    $response['mensagem'] = 'Serviço adicionado com sucesso!';
                }
            } catch (PDOException $e) {
                $response['mensagem'] = "Erro ao salvar: " . $e->getMessage();
            }
        }
    }
}

// Buscar para Edição
if (isset($_GET['acao']) && $_GET['acao'] == 'buscar') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt->execute([$id]);
    $servico = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($servico) {
        $response['sucesso'] = true;
        $response['servico'] = $servico;
    }
}

// Processar Edição
if (isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $descricao = $_POST['descricao'];

    try {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $ext;
            $dir = "../uploads/servicos/";

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
                // Buscar imagem antiga
                $stmt = $pdo->prepare("SELECT imagem FROM servicos WHERE id = ?");
                $stmt->execute([$id]);
                $imagem_antiga = $stmt->fetchColumn();

                // Apagar imagem antiga
                if ($imagem_antiga && file_exists($dir . $imagem_antiga)) {
                    unlink($dir . $imagem_antiga);
                }

                // Atualizar com nova imagem
                $stmt = $pdo->prepare("UPDATE servicos SET nome = ?, endereco = ?, telefone = ?,
                                     descricao = ?, imagem = ? WHERE id = ?");
                $stmt->execute([$nome, $endereco, $telefone, $descricao, $novo_nome, $id]);
            }
        } else {
            // Atualizar sem mudar a imagem
            $stmt = $pdo->prepare("UPDATE servicos SET nome = ?, endereco = ?, telefone = ?,
                                 descricao = ? WHERE id = ?");
            $stmt->execute([$nome, $endereco, $telefone, $descricao, $id]);
        }

        $response['sucesso'] = true;
        $response['mensagem'] = 'Serviço atualizado com sucesso!';
    } catch (PDOException $e) {
        $response['mensagem'] = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Excluir Serviço
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'];

    try {
        // Buscar imagem antes de excluir
        $stmt = $pdo->prepare("SELECT imagem FROM servicos WHERE id = ?");
        $stmt->execute([$id]);
        $imagem = $stmt->fetchColumn();

        if ($imagem) {
            $arquivo = "../uploads/servicos/" . $imagem;
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
        }

        // Excluir registro
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = ?");
        if ($stmt->execute([$id])) {
            $response['sucesso'] = true;
            $response['mensagem'] = 'Serviço excluído com sucesso!';
        }
    } catch (PDOException $e) {
        $response['mensagem'] = "Erro ao excluir: " . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
