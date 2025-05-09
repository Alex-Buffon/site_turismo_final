<?php
require_once 'contador.php';

function registrarVisita($pdo, $pagina = 'index') {
    // Já inclui a atualização do contador básico
    $contagem = atualizarContador($pdo, $pagina);

    try {
        // Registra estatísticas detalhadas
        $stmt = $pdo->prepare("SELECT
            COUNT(*) as total_visitas,
            COUNT(DISTINCT ip_visitante) as visitantes_unicos,
            SUM(paginas_visitadas) as paginas_visitadas
            FROM visitas
            WHERE DATE(data_visita) = CURRENT_DATE");
        $stmt->execute();
        $estatisticas = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'contador' => $contagem,
            'estatisticas' => $estatisticas
        ];

    } catch (PDOException $e) {
        error_log("Erro ao registrar estatísticas: " . $e->getMessage());
        return ['contador' => $contagem, 'estatisticas' => null];
    }
}
