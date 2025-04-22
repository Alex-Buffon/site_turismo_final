<?php
require_once '../includes/conexao.php';
session_start();

if(!isset($_SESSION['admin_logado'])) {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if(!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID não fornecido']);
    exit;
}

$id = $_GET['id'];
$response = ['sucesso' => false];

try {
    $stmt = $pdo->prepare("UPDATE contatos SET status = 'lido' WHERE id = ?");
    if($stmt->execute([$id])) {
        $response['sucesso'] = true;
        $response['mensagem'] = "Contato marcado como lido!";
    }
} catch(PDOException $e) {
    $response['mensagem'] = "Erro ao atualizar status: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
