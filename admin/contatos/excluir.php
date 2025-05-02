<?php
require_once '../includes/conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logado'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM contatos WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Contato excluído com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir contato']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
exit;
