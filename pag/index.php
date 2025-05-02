<?php
require_once '../admin/includes/conexao.php';

// Verificar conexão
try {
    if (!$pdo || !($pdo instanceof PDO)) {
        throw new Exception("Conexão com o banco de dados não estabelecida");
    }
    $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Array de meses em português
$mesesPT = array(
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
);

// Preparar array de 12 meses rolantes
$meses = array();
$mesAtual = date('n'); // Mês atual (1-12)
$anoAtual = date('Y');

for ($i = 0; $i < 12; $i++) {
    $timestamp = strtotime("+$i months");
    $mes = date('n', $timestamp);
    $ano = date('Y', $timestamp);
    $meses[] = array(
        'numero' => $mes,
        'nome' => $mesesPT[$mes],
        'ano' => $ano
    );
}

// Consultas para apoiadores
$stmtApoiadoresEsq = $pdo->query("SELECT imagem, site FROM apoiadores WHERE posicao = 'esquerda' ORDER BY ordem ASC");
$apoiadoresEsquerda = $stmtApoiadoresEsq->fetchAll(PDO::FETCH_ASSOC);

$stmtApoiadoresDir = $pdo->query("SELECT imagem, site FROM apoiadores WHERE posicao = 'direita' ORDER BY ordem ASC");
$apoiadoresDireita = $stmtApoiadoresDir->fetchAll(PDO::FETCH_ASSOC);

// Buscar imagens da galeria home
$stmt = $pdo->query("SELECT * FROM galerias WHERE tipo = 'home' ORDER BY ordem ASC");
$galeria_home = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Header Principal -->
    <header class="header-principal">
        <!-- Menu Desktop -->
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

                <!-- Menu Mobile Toggle -->
                <div class="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>

        <!-- Menu Mobile -->
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

    <!-- Layout Principal -->
    <div class="main-layout">
        <!-- Apoiadores Esquerda -->
        <aside class="apoiadores-left">
            <div class="apoiadores-grid">
                <?php foreach ($apoiadoresEsquerda as $apoiador): ?>
                    <a href="<?php echo htmlspecialchars($apoiador['site']); ?>"
                        target="_blank"
                        class="apoiador-card">
                        <img src="../admin/uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                            alt="Logo Apoiador">
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content" id="inicio">
            <!-- Seção Galeria -->
            <section class="galeria-section section-spacing" id="galeria">
                <div class="galeria-container">
                    <div class="section-title">
                        <h2><i class="fas fa-images"></i> Descubra Caxias do Sul</h2>
                        <p>Experiências únicas em cada roteiro turístico</p>
                    </div>
                    <div class="galeria-grid">
                        <?php foreach ($galeria_home as $imagem): ?>
                            <div class="galeria-item" <?php echo !empty($imagem['url']) ? 'data-url="' . htmlspecialchars($imagem['url']) . '"' : ''; ?>>
                                <div class="galeria-imagem">
                                    <img src="../admin/uploads/galerias/<?php echo htmlspecialchars($imagem['imagem']); ?>"
                                        alt="<?php echo htmlspecialchars($imagem['titulo']); ?>"
                                        loading="lazy">
                                    <?php if (!empty($imagem['url'])): ?>
                                        <div class="link-indicator">
                                            <i class="fas fa-external-link-alt"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="galeria-info">
                                    <h3><?php echo htmlspecialchars($imagem['titulo']); ?></h3>
                                    <?php if (!empty($imagem['descricao'])): ?>
                                        <p><?php echo htmlspecialchars($imagem['descricao']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Seção de Eventos -->
            <section id="eventos" class="eventos-section section-spacing">
                <div class="eventos-container">
                    <div class="section-title">
                        <h2><i class="fas fa-calendar"></i> Próximos Eventos</h2>
                        <p>Momentos especiais te esperam aqui</p>
                    </div>

                    <!-- Tabs dos Meses -->
                    <div class="meses-tabs">
                        <?php foreach ($meses as $index => $mes): ?>
                            <button class="mes-tab <?php echo ($index === 0) ? 'active' : ''; ?>"
                                data-mes="<?php echo $mes['numero']; ?>"
                                data-ano="<?php echo $mes['ano']; ?>">
                                <?php echo $mes['nome']; ?> / <?php echo $mes['ano']; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Grid de Eventos -->
                    <div class="eventos-container">
                        <?php foreach ($meses as $index => $mes): ?>
                            <div class="eventos-grid"
                                id="mes-<?php echo $mes['numero']; ?>"
                                data-mes="<?php echo $mes['numero']; ?>"
                                style="display: <?php echo $index === 0 ? 'grid' : 'none'; ?>">
                                <?php
                                $stmtEventos = $pdo->prepare("SELECT * FROM eventos WHERE MONTH(data_inicio) = ? AND YEAR(data_inicio) = ? ORDER BY data_inicio");
                                $stmtEventos->execute([$mes['numero'], $mes['ano']]);

                                if ($stmtEventos->rowCount() > 0):
                                    while ($evento = $stmtEventos->fetch(PDO::FETCH_ASSOC)):
                                        $dataEvento = strtotime($evento['data_inicio']);
                                        $diaEvento = date('d', $dataEvento);
                                        $mesEvento = $mesesPT[date('n', $dataEvento)];
                                ?>
                                        <div class="evento-card" data-info='<?php echo json_encode([
                                                                                "id" => $evento['id'],
                                                                                "titulo" => $evento['titulo'],
                                                                                "imagem" => $evento['imagem'],
                                                                                "local" => $evento['local'],
                                                                                "dia" => $diaEvento,
                                                                                "mes" => $mesEvento,
                                                                                "data_completa" => $evento['data_inicio']
                                                                            ]); ?>'>
                                            <div class="evento-imagem">
                                                <img src="../admin/uploads/eventos/<?php echo $evento['imagem']; ?>"
                                                    alt="<?php echo htmlspecialchars($evento['titulo']); ?>">
                                                <div class="evento-data">
                                                    <span class="dia"><?php echo $diaEvento; ?></span>
                                                    <span class="mes"><?php echo $mesEvento; ?></span>
                                                </div>
                                            </div>
                                            <div class="evento-info">
                                                <h3><?php echo htmlspecialchars($evento['titulo']); ?></h3>
                                                <p class="evento-local">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <?php echo htmlspecialchars($evento['local']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <div class="sem-eventos">
                                        <p>Nenhum evento programado para <?php echo $mes['nome']; ?> de <?php echo $mes['ano']; ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Modal de Eventos -->
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
        </main>

        <!-- Apoiadores Mobile -->
        <div class="mobile-apoiadores">
            <div class="section-title">
                <h2><i class="fas fa-handshake"></i> Parceiros do Turismo</h2>
                <p>Empresas que fazem a diferença</p>
            </div>

            <div class="apoiadores-grid">
                <?php
                // Combina os apoiadores da esquerda e direita
                $todosApoiadores = array_merge($apoiadoresEsquerda, $apoiadoresDireita);
                foreach ($todosApoiadores as $apoiador):
                ?>
                    <a href="<?php echo htmlspecialchars($apoiador['site']); ?>"
                        target="_blank"
                        class="apoiador-card">
                        <img src="../admin/uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                            alt="Logo Apoiador"
                            loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        </div>



        <!-- Seção de Contato -->
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
    </div>
    </section>



    <!-- Apoiadores Direita -->
    <aside class="apoiadores-right">
        <div class="apoiadores-grid">
            <?php foreach ($apoiadoresDireita as $apoiador): ?>
                <a href="<?php echo htmlspecialchars($apoiador['site']); ?>"
                    target="_blank"
                    class="apoiador-card">
                    <img src="../admin/uploads/apoiadores/<?php echo $apoiador['imagem']; ?>"
                        alt="Logo Apoiador">
                </a>
            <?php endforeach; ?>
        </div>
    </aside>
    </div>



    <!-- Footer -->
    <footer class="app-footer">
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Informações de Contato</h3>
                    <ul class="footer-contato">
                        <li class="footer-item">
                            <a href="https://wa.me/5554981486589" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                                <span>(54) 98148-6589</span>
                            </a>
                        </li>
                        <li class="footer-item">
                            <a href="mailto:contato@exemplo.com">
                                <i class="fas fa-envelope"></i>
                                <span>contato@exemplo.com</span>
                            </a>
                        </li>
                        <li class="footer-item">
                            <a href="https://maps.google.com" target="_blank">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Rua Exemplo, 123 - Cidade</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Conecte-se Conosco</h3>
                    <div class="social-links">
                        <a href="#" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank" title="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                    <p class="social-desc">Siga nossas redes sociais e fique por dentro de todas as novidades</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Portal de Turismo. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/calendario.js"></script>
    <script src="js/apoiadores.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/contato.js"></script>
    <script src="js/galerias.js"> </script>
</body>

</html>
