<?php
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Busca imagem para deletar
        $stmt = $pdo->prepare("SELECT imagem FROM apoiadores WHERE id = ?");
        $stmt->execute([$id]);
        $imagem = $stmt->fetchColumn();

        if ($imagem && file_exists("../uploads/apoiadores/$imagem")) {
            unlink("../uploads/apoiadores/$imagem");
        }

        $stmt = $pdo->prepare("DELETE FROM apoiadores WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: index.php?success=1');
        exit;
    } catch (PDOException $e) {
        die('Erro ao excluir: ' . $e->getMessage());
    }
}

header('Location: index.php');
