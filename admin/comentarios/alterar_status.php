<?php
require_once '../includes/conexao.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['admin_logado'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['status'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Parâmetros inválidos']);
    exit;
}

$id = $_GET['id'];
$status = $_GET['status'];

// Validar status permitidos
$statusPermitidos = ['pendente', 'aprovado', 'reprovado'];
if(!in_array($status, $statusPermitidos)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Status inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE comentarios SET status = ? WHERE id = ?");
    if($stmt->execute([$status, $id])) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status alterado com sucesso']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao alterar status']);
    }
} catch(PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
