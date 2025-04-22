<?php
require_once '../includes/header.php';

if(!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

try {
    // Busca imagem antes de excluir
    $stmt = $pdo->prepare("SELECT imagem FROM apoiadores WHERE id = ?");
    $stmt->execute([$id]);
    $apoiador = $stmt->fetch();

    // Exclui o registro
    $stmt = $pdo->prepare("DELETE FROM apoiadores WHERE id = ?");
    $stmt->execute([$id]);

    // Remove a imagem se existir
    if($apoiador['imagem']) {
        $arquivo = "../uploads/apoiadores/" . $apoiador['imagem'];
        if(file_exists($arquivo)) {
            unlink($arquivo);
        }
    }

    header('Location: index.php?msg=excluido');
} catch(PDOException $e) {
    die("Erro ao excluir: " . $e->getMessage());
}
