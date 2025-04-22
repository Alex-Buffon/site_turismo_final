<?php
require_once '../includes/conexao.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, titulo as title, data_inicio as start, data_fim as end FROM eventos");
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($eventos);
} catch(PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
