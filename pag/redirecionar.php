<?php
require_once '../admin/includes/conexao.php';
require_once '../admin/includes/rastreamento.php';

if (isset($_GET['tipo']) && isset($_GET['id'])) {
    $tipo = $_GET['tipo'];
    $id = (int)$_GET['id'];

    try {
        // Busca informações do item baseado no tipo
        switch ($tipo) {
            case 'galeria-home':
                $stmt = $pdo->prepare("SELECT titulo, url FROM galerias WHERE id = ? AND tipo = 'home'");
                break;
            case 'galeria-servicos':
                $stmt = $pdo->prepare("SELECT titulo, url FROM galerias WHERE id = ? AND tipo = 'servicos'");
                break;
            case 'apoiador':
                $stmt = $pdo->prepare("SELECT nome as titulo, site as url FROM apoiadores WHERE id = ?");
                break;
            case 'servico':
                $stmt = $pdo->prepare("SELECT nome as titulo, url FROM servicos WHERE id = ?");
                break;
            default:
                throw new Exception("Tipo inválido");
        }

        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item && !empty($item['url'])) {
            // Registra o clique
            registrarClique($pdo, $tipo, $id, $item['titulo'], $item['url']);

            // Redireciona
            header("Location: " . $item['url']);
            exit;
        }
    } catch (Exception $e) {
        error_log("Erro no redirecionamento: " . $e->getMessage());
    }
}

// Se algo der errado, redireciona para a home
header("Location: index.php");
exit;
