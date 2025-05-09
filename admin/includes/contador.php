<?php
function atualizarContador($pdo, $pagina = 'index') {
    try {
        // Verifica se a tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'visitas'");
        if ($stmt->rowCount() == 0) {
            // Cria a tabela se não existir
            $pdo->exec("CREATE TABLE IF NOT EXISTS visitas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                pagina VARCHAR(100) NOT NULL,
                contador BIGINT DEFAULT 1,
                data_ultima_visita TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                data_visita DATETIME DEFAULT CURRENT_TIMESTAMP,
                ip_visitante VARCHAR(45),
                dispositivo VARCHAR(255),
                pagina_origem VARCHAR(255),
                paginas_visitadas INT DEFAULT 1,
                UNIQUE KEY (pagina)
            )");
        }

        // Insere ou atualiza o contador
        $stmt = $pdo->prepare("
            INSERT INTO visitas (
                pagina, contador, ip_visitante, dispositivo, pagina_origem, data_visita
            ) VALUES (
                :pagina, 1, :ip, :dispositivo, :origem, NOW()
            ) ON DUPLICATE KEY UPDATE
                contador = contador + 1,
                data_ultima_visita = NOW(),
                ip_visitante = :ip,
                dispositivo = :dispositivo,
                pagina_origem = :origem
        ");

        // Coleta informações da visita
        $ip = $_SERVER['REMOTE_ADDR'];
        $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
        $origem = $_SERVER['HTTP_REFERER'] ?? 'Acesso direto';

        $stmt->execute([
            ':pagina' => $pagina,
            ':ip' => $ip,
            ':dispositivo' => $dispositivo,
            ':origem' => $origem
        ]);

        // Busca o valor atual do contador
        $stmt = $pdo->prepare("SELECT contador FROM visitas WHERE pagina = ?");
        $stmt->execute([$pagina]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['contador'] ?? 0;

    } catch (PDOException $e) {
        error_log("Erro no contador: " . $e->getMessage());
        return 0;
    }
}
