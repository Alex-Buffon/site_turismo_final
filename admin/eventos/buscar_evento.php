<?php
require_once '../includes/conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logado'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $evento = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($evento) {
            // Formata a data para o formato aceito pelo input date
            $evento['data_inicio'] = date('Y-m-d', strtotime($evento['data_inicio']));
            echo json_encode($evento);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Evento não encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
    }
}
