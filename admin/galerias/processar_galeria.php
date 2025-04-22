<?php
require_once '../includes/conexao.php';
session_start();

if(!isset($_SESSION['admin_logado'])) {
    die('Acesso negado');
}

$response = ['sucesso' => false, 'mensagem' => ''];

// Processar Adição de Nova Imagem
if(isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $novo_nome = uniqid() . '.' . $ext;
        $dir = "../uploads/galeria/";

        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if(move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO galerias (tipo, titulo, descricao, imagem) VALUES (?, ?, ?, ?)");
                if($stmt->execute([$tipo, $titulo, $descricao, $novo_nome])) {
                    $response['sucesso'] = true;
                    $response['mensagem'] = 'Imagem adicionada com sucesso!';
                }
            } catch(PDOException $e) {
                $response['mensagem'] = "Erro ao salvar: " . $e->getMessage();
            }
        } else {
            $response['mensagem'] = "Erro ao fazer upload da imagem";
        }
    } else {
        $response['mensagem'] = "Por favor, selecione uma imagem";
    }
}

// Buscar Imagem para Edição
if(isset($_GET['acao']) && $_GET['acao'] == 'buscar') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM galerias WHERE id = ?");
    $stmt->execute([$id]);
    $imagem = $stmt->fetch(PDO::FETCH_ASSOC);

    if($imagem) {
        $response['sucesso'] = true;
        $response['imagem'] = $imagem;
    }
}

// Processar Edição
if(isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $tipo = $_POST['tipo'];

    try {
        if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . '.' . $ext;
            $dir = "../uploads/galeria/";

            if(move_uploaded_file($_FILES['imagem']['tmp_name'], $dir . $novo_nome)) {
                $stmt = $pdo->prepare("SELECT imagem FROM galerias WHERE id = ?");
                $stmt->execute([$id]);
                $imagem_antiga = $stmt->fetchColumn();

                if($imagem_antiga && file_exists($dir . $imagem_antiga)) {
                    unlink($dir . $imagem_antiga);
                }

                $stmt = $pdo->prepare("UPDATE galerias SET titulo = ?, descricao = ?, imagem = ? WHERE id = ?");
                $stmt->execute([$titulo, $descricao, $novo_nome, $id]);
                $response['sucesso'] = true;
                $response['mensagem'] = 'Imagem atualizada com sucesso!';
            }
        } else {
            $stmt = $pdo->prepare("UPDATE galerias SET titulo = ?, descricao = ? WHERE id = ?");
            $stmt->execute([$titulo, $descricao, $id]);
            $response['sucesso'] = true;
            $response['mensagem'] = 'Dados atualizados com sucesso!';
        }
    } catch(PDOException $e) {
        $response['mensagem'] = "Erro ao atualizar: " . $e->getMessage();
    }
}

// Excluir Imagem
if(isset($_GET['acao']) && $_GET['acao'] == 'excluir') {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT imagem FROM galerias WHERE id = ?");
        $stmt->execute([$id]);
        $imagem = $stmt->fetchColumn();

        if($imagem) {
            $arquivo = "../uploads/galeria/" . $imagem;
            if(file_exists($arquivo)) {
                unlink($arquivo);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM galerias WHERE id = ?");
        if($stmt->execute([$id])) {
            $response['sucesso'] = true;
            $response['mensagem'] = 'Imagem excluída com sucesso!';
        }
    } catch(PDOException $e) {
        $response['mensagem'] = "Erro ao excluir: " . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
