<?php
require_once '../includes/conexao.php';
session_start();

header('Content-Type: application/json');

// Verifica autenticação
if (!isset($_SESSION['admin_logado'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autorizado']);
    exit;
}

// Verifica se ID foi fornecido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido ou não fornecido']);
    exit;
}

try {
    // Inicia transação
    $pdo->beginTransaction();

    // Primeiro busca a imagem do evento
    $stmt = $pdo->prepare("SELECT imagem FROM eventos WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $imagem = $stmt->fetchColumn();

    // Remove a imagem se existir
    if ($imagem) {
        $caminho_imagem = "../uploads/eventos/{$imagem}";
        if (file_exists($caminho_imagem)) {
            if (!unlink($caminho_imagem)) {
                throw new Exception('Erro ao remover imagem do evento');
            }
        }
    }

    // Exclui o evento
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id = ?");
    $sucesso = $stmt->execute([(int)$_GET['id']]);

    if ($sucesso && $stmt->rowCount() > 0) {
        $pdo->commit();
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Evento excluído com sucesso'
        ]);
    } else {
        throw new Exception('Evento não encontrado ou já foi excluído');
    }
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao excluir evento: ' . $e->getMessage()
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
}
