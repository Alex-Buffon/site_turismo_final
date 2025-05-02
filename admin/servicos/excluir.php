<?php
require_once '../includes/conexao.php';
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

try {
    // Buscar tipo e imagem antes de excluir
    $stmt = $pdo->prepare("SELECT tipo, imagem FROM servicos WHERE id = ?");
    $stmt->execute([$id]);
    $servico = $stmt->fetch();

    if ($servico) {
        // Excluir imagem se existir
        if ($servico['imagem']) {
            $arquivo = "../uploads/servicos/" . $servico['imagem'];
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
        }

        // Excluir registro
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['mensagem'] = "Serviço excluído com sucesso!";
    }

    header('Location: index.php?tipo=' . $servico['tipo']);
} catch (PDOException $e) {
    die("Erro ao excluir: " . $e->getMessage());
}
