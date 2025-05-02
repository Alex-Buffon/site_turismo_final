<?php
require_once '../includes/conexao.php';

// Habilita exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug dos dados recebidos
        $input = file_get_contents('php://input');
        error_log("Dados recebidos: " . print_r($_POST, true));

        // Validação dos campos
        if (
            empty($_POST['nome']) || empty($_POST['email']) ||
            empty($_POST['telefone']) || empty($_POST['mensagem'])
        ) {
            throw new Exception("Todos os campos são obrigatórios");
        }

        // Sanitização dos dados
        $dados = [
            'nome' => trim(strip_tags($_POST['nome'])),
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'telefone' => trim(strip_tags($_POST['telefone'])),
            'mensagem' => trim(strip_tags($_POST['mensagem'])),
            'data_envio' => date('Y-m-d H:i:s')
        ];

        // Validações específicas
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido');
        }

        // Inserção simplificada para teste
        $sql = "INSERT INTO contatos (nome, email, telefone, mensagem, data_envio)
                VALUES (:nome, :email, :telefone, :mensagem, :data_envio)";

        $stmt = $pdo->prepare($sql);

        if (!$stmt->execute($dados)) {
            error_log("Erro PDO: " . print_r($stmt->errorInfo(), true));
            throw new Exception('Erro ao salvar no banco de dados');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Mensagem enviada com sucesso!'
        ]);
    } catch (Exception $e) {
        error_log("Erro na execução: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
