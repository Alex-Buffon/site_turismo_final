<?php
function registrarClique($pdo, $tipo, $id, $titulo, $url) {
    try {
        // Verifica se a tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'rastreamento_cliques'");
        if ($stmt->rowCount() == 0) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS rastreamento_cliques (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tipo VARCHAR(50) NOT NULL,
                item_id INT NOT NULL,
                titulo VARCHAR(255),
                url_destino VARCHAR(512),
                ip_visitante VARCHAR(45),
                dispositivo VARCHAR(255),
                origem VARCHAR(255),
                data_clique TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        $stmt = $pdo->prepare("
            INSERT INTO rastreamento_cliques (
                tipo, item_id, titulo, url_destino, ip_visitante, dispositivo, origem
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $tipo,
            $id,
            $titulo,
            $url,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido',
            $_SERVER['HTTP_REFERER'] ?? 'Acesso direto'
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Erro ao registrar clique: " . $e->getMessage());
        return false;
    }
}
