<?php
session_start();
require_once '../admin/includes/conexao.php';

// Verificação de conexão e contador
try {
    if (!$pdo || !($pdo instanceof PDO)) {
        throw new Exception("Conexão com o banco de dados não estabelecida");
    }

    // Contador de visitas
    $totalVisitas = 0;
    if (file_exists('../admin/includes/contador.php')) {
        require_once '../admin/includes/contador.php';
        $totalVisitas = atualizarContador($pdo, 'index');
    }

    // Meses em português
    $mesesPT = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
        4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
        7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
        10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];

    // Array de meses rolantes
    $meses = [];
    for ($i = 0; $i < 12; $i++) {
        $timestamp = strtotime("+$i months");
        $mes = date('n', $timestamp);
        $ano = date('Y', $timestamp);
        $meses[] = [
            'numero' => $mes,
            'nome' => $mesesPT[$mes],
            'ano' => $ano
        ];
    }

    // Consultas
    $apoiadoresEsquerda = $pdo->query("SELECT id, imagem, site FROM apoiadores WHERE posicao = 'esquerda' ORDER BY ordem ASC")->fetchAll(PDO::FETCH_ASSOC);
    $apoiadoresDireita = $pdo->query("SELECT id, imagem, site FROM apoiadores WHERE posicao = 'direita' ORDER BY ordem ASC")->fetchAll(PDO::FETCH_ASSOC);
    $galeria_home = $pdo->query("SELECT id, imagem, url, titulo, descricao FROM galerias WHERE tipo = 'home' ORDER BY ordem ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Erro: " . $e->getMessage());
    die("Ocorreu um erro. Por favor, tente novamente mais tarde.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Turismo</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/calendario.css">
    <link rel="stylesheet" href="css/apoiadores.css">
    <link rel="stylesheet" href="css/menu.css">
    <link rel="stylesheet" href="css/contato.css">
    <link rel="stylesheet" href="css/galerias.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="layout-wrapper">
        <header class="header-principal">
            <nav class="nav-desktop">
                <div class="container">
                    <div class="logo-header">
                        <img src="../pag/img/iStock-536613027.webp" alt="Logo Turismo">
                    </div>
                    <ul class="menu-desktop">
                        <li><a href="#inicio"><i class="fas fa-home"></i> Início</a></li>
                        <li><a href="#galeria"><i class="fas fa-images"></i> Galeria</a></li>
                        <li><a href="#eventos"><i class="fas fa-calendar"></i> Eventos</a></li>
                        <li><a href="#contato"><i class="fas fa-envelope"></i> Contato</a></li>
                    </ul>
                    <div class="menu-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </nav>

            <div class="menu-lateral">
                <div class="menu-header">
                    <img src="../pag/img/iStock-536613027.webp" alt="Logo Turismo" class="logo-menu">
                    <button class="fechar-menu">&times;</button>
                </div>
                <ul class="menu-items">
                    <li><a href="#inicio"><i class="fas fa-home"></i> Início</a></li>
                    <li><a href="#galeria"><i class="fas fa-images"></i> Galeria</a></li>
                    <li><a href="#eventos"><i class="fas fa-calendar"></i> Eventos</a></li>
                    <li><a href="#contato"><i class="fas fa-envelope"></i> Contato</a></li>
                </ul>
            </div>
        </header>

        <main class="main-content">
            <aside class="apoiadores-left">
                <div class="apoiadores-grid">
                    <?php foreach ($apoiadoresEsquerda as $apoiador): ?>
                        <a href="redirecionar.php?tipo=apoiador&id=<?= $apoiador['id'] ?>"
                           target="_blank"
                           class="apoiador-card">
                            <img src="../admin/uploads/apoiadores/<?= $apoiador['imagem'] ?>"
                                 alt="Logo Apoiador">
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="content-center">
                <section class="galeria-section section-spacing" id="galeria">
                    <div class="galeria-container">
                        <div class="section-title">
                            <h2><i class="fas fa-images"></i> Descubra Caxias do Sul</h2>
                            <p>Experiências únicas em cada roteiro turístico</p>
                        </div>
                        <div class="galeria-grid">
                            <?php foreach ($galeria_home as $imagem): ?>
                                <?php if (!empty($imagem['url'])): ?>
                                    <a href="redirecionar.php?tipo=galeria-home&id=<?= $imagem['id'] ?>"
                                       target="_blank"
                                       class="galeria-item">
                                <?php else: ?>
                                    <div class="galeria-item">
                                <?php endif; ?>

                                <div class="galeria-imagem">
                                    <img src="../admin/uploads/galerias/<?= htmlspecialchars($imagem['imagem']) ?>"
                                         alt="<?= htmlspecialchars($imagem['titulo']) ?>"
                                         loading="lazy">
                                    <?php if (!empty($imagem['url'])): ?>
                                        <div class="link-indicator">
                                            <i class="fas fa-external-link-alt"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="galeria-info">
                                    <h3><?= htmlspecialchars($imagem['titulo']) ?></h3>
                                    <?php if (!empty($imagem['descricao'])): ?>
                                        <p><?= htmlspecialchars($imagem['descricao']) ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($imagem['url'])): ?>
                                    </a>
                                <?php else: ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                <section id="eventos" class="eventos-section section-spacing">
                    <div class="eventos-container">
                        <div class="section-title">
                            <h2><i class="fas fa-calendar"></i> Próximos Eventos</h2>
                            <p>Momentos especiais te esperam aqui</p>
                        </div>

                        <div class="meses-tabs">
                            <?php foreach ($meses as $index => $mes): ?>
                                <button class="mes-tab <?= ($index === 0) ? 'active' : '' ?>"
                                        data-mes="<?= $mes['numero'] ?>"
                                        data-ano="<?= $mes['ano'] ?>">
                                    <?= $mes['nome'] ?> / <?= $mes['ano'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="eventos-container">
                            <?php foreach ($meses as $index => $mes): ?>
                                <div class="eventos-grid"
                                     id="mes-<?= $mes['numero'] ?>"
                                     data-mes="<?= $mes['numero'] ?>"
                                     style="display: <?= $index === 0 ? 'grid' : 'none' ?>">
                                    <?php
                                    $stmtEventos = $pdo->prepare("SELECT * FROM eventos WHERE MONTH(data_inicio) = ? AND YEAR(data_inicio) = ? ORDER BY data_inicio");
                                    $stmtEventos->execute([$mes['numero'], $mes['ano']]);

                                    if ($stmtEventos->rowCount() > 0):
                                        while ($evento = $stmtEventos->fetch(PDO::FETCH_ASSOC)):
                                            $dataEvento = strtotime($evento['data_inicio']);
                                            $diaEvento = date('d', $dataEvento);
                                            $mesEvento = $mesesPT[date('n', $dataEvento)];
                                    ?>
                                            <div class="evento-card" data-info='<?= json_encode([
                                                "id" => $evento['id'],
                                                "titulo" => $evento['titulo'],
                                                "imagem" => $evento['imagem'],
                                                "local" => $evento['local'],
                                                "dia" => $diaEvento,
                                                "mes" => $mesEvento,
                                                "data_completa" => $evento['data_inicio']
                                            ]) ?>'>
                                                <div class="evento-imagem">
                                                    <img src="../admin/uploads/eventos/<?= $evento['imagem'] ?>"
                                                         alt="<?= htmlspecialchars($evento['titulo']) ?>">
                                                    <div class="evento-data">
                                                        <span class="dia"><?= $diaEvento ?></span>
                                                        <span class="mes"><?= $mesEvento ?></span>
                                                    </div>
                                                </div>
                                                <div class="evento-info">
                                                    <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                                                    <p class="evento-local">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <?= htmlspecialchars($evento['local']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endwhile;
                                    else: ?>
                                        <div class="sem-eventos">
                                            <p>Nenhum evento programado para <?= $mes['nome'] ?> de <?= $mes['ano'] ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="modal-evento" class="modal">
                            <div class="modal-content">
                                <span class="fechar">&times;</span>
                                <div id="evento-detalhes">
                                    <div class="navegacao-eventos">
                                        <button class="nav-btn prev" style="display: none;">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button class="nav-btn next" style="display: none;">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="evento-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="contato" class="contato-section">
                    <div class="section-title">
                        <h2><i class="fas fa-envelope"></i> Fale com a Gente</h2>
                        <p>Estamos prontos para te ajudar a viver as melhores experiências</p>
                    </div>

                    <div class="contato-wrapper">
                        <form id="formContato" class="contato-form" action="processar_contato.php" method="POST">
                            <div class="form-group">
                                <label for="nome"><i class="fas fa-user"></i> Nome</label>
                                <input type="text" id="nome" name="nome" required>
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                                <input type="tel" id="telefone" name="telefone" required>
                            </div>

                            <div class="form-group">
                                <label for="mensagem"><i class="fas fa-comment"></i> Mensagem</label>
                                <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
                            </div>

                            <button type="submit" class="btn-enviar">
                                <i class="fas fa-paper-plane"></i> Enviar Mensagem
                            </button>
                        </form>
                    </div>
                </section>
            </div>

            <aside class="apoiadores-right">
                <div class="apoiadores-grid">
                    <?php foreach ($apoiadoresDireita as $apoiador): ?>
                        <a href="<?= htmlspecialchars($apoiador['site']) ?>"
                           target="_blank"
                           class="apoiador-card">
                            <img src="../admin/uploads/apoiadores/<?= $apoiador['imagem'] ?>"
                                 alt="Logo Apoiador">
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>
        </main>

        <div class="mobile-apoiadores">
            <div class="section-title">
                <h2><i class="fas fa-handshake"></i> Parceiros do Turismo</h2>
                <p>Empresas que fazem a diferença</p>
            </div>

            <div class="apoiadores-grid">
                <?php
                $todosApoiadores = array_merge($apoiadoresEsquerda, $apoiadoresDireita);
                foreach ($todosApoiadores as $apoiador):
                ?>
                    <a href="redirecionar.php?tipo=apoiador&id=<?= $apoiador['id'] ?>"
                       target="_blank"
                       class="apoiador-card">
                        <img src="../admin/uploads/apoiadores/<?= $apoiador['imagem'] ?>"
                             alt="Logo Apoiador">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <footer class="app-footer">
            <div class="footer-content">
                <div class="footer-grid">
                    <div class="footer-col">
                        <h3>Informações de Contato</h3>
                        <ul class="footer-contato">
                            <li class="footer-item">
                                <a href="https://wa.me/5554981222284" target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>(54) 9814822-2284 (Carla)</span>
                                </a>
                            </li>
                            <li class="footer-item">
                                <a href="mailto:contato@exemplo.com">
                                    <i class="fas fa-envelope"></i>
                                    <span>contato@exemplo.com</span>
                                </a>
                            </li>
                            <li class="footer-item">
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode('R. Guerino Zugno, 17 - Samuara, Caxias do Sul - RS, 95180-000') ?>"
                                   target="_blank">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>R. Guerino Zugno, 1700 - Samuara, Caxias do Sul - RS</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h3>Conecte-se Conosco</h3>
                        <div class="social-links">
                            <a href="https://www.facebook.com/share/16E44oNUUC/" target="_blank" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://www.instagram.com/caxiastemturismo?igsh=NTNnbWViZGdybm43" target="_blank" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" target="_blank" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="#" target="_blank" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                        <p class="social-desc">Siga nossas redes sociais e fique por dentro de todas as novidades</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; <?= date('Y') ?> Portal de Turismo. Todos os direitos reservados.</p>
                    <p class="contador-visitas">
                        <i class="fas fa-chart-line"></i>
                        Total de visitas: <?= number_format($totalVisitas, 0, ',', '.') ?>
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/calendario.js"></script>
    <script src="js/apoiadores.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/contato.js"></script>
    <script src="js/galerias.js"></script>
</body>
</html>
