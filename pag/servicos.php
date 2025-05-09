<?php
require_once '../admin/includes/conexao.php';

// Inclui o contador com verificação
$totalVisitas = 0;
if (file_exists('../admin/includes/contador.php')) {
  require_once '../admin/includes/contador.php';

  // Coleta informações da visita
  $ip = $_SERVER['REMOTE_ADDR'];
  $dispositivo = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
  $origem = $_SERVER['HTTP_REFERER'] ?? 'Acesso direto';

  // Atualiza o contador para a página serviços
  $totalVisitas = atualizarContador($pdo, 'servicos');
}

// Verificar conexão
try {
  if (!$pdo || !($pdo instanceof PDO)) {
    throw new Exception("Conexão com o banco de dados não estabelecida");
  }
  $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
} catch (Exception $e) {
  die("Erro de conexão: " . $e->getMessage());
}


function getIconeCategoria($tipo)
{
  $icones = [
    'hotel' => 'hotéis',
    'pousada' => 'bed',
    'restaurante' => 'utensils',
    'lanchonete' => 'hamburger',
    'passeio' => 'hiking'
  ];
  return $icones[$tipo] ?? 'concierge-bell';
}

//Buscar serviços por categoria
$categorias = [
  'hotel' => 'Hotéis',
  'pousada' => 'Pousadas',
  'restaurante' => 'Restaurantes',
  'lanchonete' => 'Lanchonetes',
  'passeio' => 'Passeios'
];

$servicos = [];
try {
  foreach ($categorias as $tipo => $nome) {
    $stmt = $pdo->prepare("SELECT * FROM servicos WHERE tipo = ? ORDER BY nome ASC");
    $stmt->execute([$tipo]);
    $servicos[$tipo] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  error_log("Erro ao buscar serviços: " . $e->getMessage());
}

// Consultas para apoiadores
$stmtApoiadoresEsq = $pdo->query("SELECT id, imagem, site FROM apoiadores WHERE posicao = 'esquerda' ORDER BY ordem ASC");
$apoiadoresEsquerda = $stmtApoiadoresEsq->fetchAll(PDO::FETCH_ASSOC);

$stmtApoiadoresDir = $pdo->query("SELECT id, imagem, site FROM apoiadores WHERE posicao = 'direita' ORDER BY ordem ASC");
$apoiadoresDireita = $stmtApoiadoresDir->fetchAll(PDO::FETCH_ASSOC);

// Buscar serviços da galeria
try {
  $stmtServicos = $pdo->prepare("
        SELECT
            id,
            imagem,
            titulo,
            descricao,
            url,
            ordem
        FROM galerias
        WHERE tipo = 'servicos'
        ORDER BY ordem ASC
    ");
  $stmtServicos->execute();
  $galeria_servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $galeria_servicos = [];
  error_log("Erro ao buscar serviços: " . $e->getMessage());
}

// Buscar serviços por categoria com campos específicos
try {
  foreach ($categorias as $tipo => $nome) {
    $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                imagem,
                endereco,
                telefone,
                descricao,
                url
            FROM servicos
            WHERE tipo = ?
            ORDER BY nome ASC
        ");
    $stmt->execute([$tipo]);
    $servicos[$tipo] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  error_log("Erro ao buscar serviços: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Porta de Serviços</title>
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet" href="css/calendario.css">
  <link rel="stylesheet" href="css/apoiadores.css">
  <link rel="stylesheet" href="css/menu.css">
  <link rel="stylesheet" href="css/contato.css">
  <link rel="stylesheet" href="css/galerias.css">
  <link rel="stylesheet" href="css/servicos.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<header class="header-principal">
  <!-- Menu Desktop -->
  <nav class="nav-desktop">
    <div class="container">
      <div class="logo-header">
        <img src="../pag/img/iStock-536613027.webp" alt="Logo Turismo">
      </div>

      <ul class="menu-desktop">
        <li><a href="#inicio"><i class="fas fa-home"></i> Início</a></li>
        <li class="menu-dropdown">
          <a href="#servicos">
            <i class="fas fa-concierge-bell"></i> Serviços
            <i class="fas fa-chevron-down"></i>
          </a>
          <ul class="submenu">
            <li><a href="#hotel-grid"><i class="fas fa-hotel"></i> Hotéis</a></li>
            <li><a href="#pousada-grid"><i class="fas fa-bed"></i> Pousadas</a></li>
            <li><a href="#restaurante-grid"><i class="fas fa-utensils"></i> Restaurantes</a></li>
            <li><a href="#lanchonete-grid"><i class="fas fa-hamburger"></i> Lanchonetes</a></li>
            <li><a href="#passeio-grid"><i class="fas fa-hiking"></i> Passeios</a></li>
          </ul>
        </li>
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
      <li class="menu-dropdown">
        <a href="#servicos">
          <i class="fas fa-concierge-bell"></i> Serviços
          <i class="fas fa-chevron-down submenu-toggle"></i>
        </a>
        <ul class="submenu-mobile">
          <li><a href="#hotel-grid"><i class="fas fa-hotel"></i> Hotéis</a></li>
          <li><a href="#pousada-grid"><i class="fas fa-bed"></i> Pousadas</a></li>
          <li><a href="#restaurante-grid"><i class="fas fa-utensils"></i> Restaurantes</a></li>
          <li><a href="#lanchonete-grid"><i class="fas fa-hamburger"></i> Lanchonetes</a></li>
          <li><a href="#passeio-grid"><i class="fas fa-hiking"></i> Passeios</a></li>
        </ul>
      </li>
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
    <!-- Seção de Serviços -->
    <section class="servicos-section section-spacing" id="servicos">
      <div class="servicos-container">
        <div class="section-title">
          <h2><i class="fas fa-concierge-bell"></i> Nossos Serviços</h2>
          <p>Conheça todas as opções disponíveis para sua melhor experiência</p>
        </div>

        <!-- Navegação das categorias -->
        <div class="categorias-nav">
          <?php foreach ($categorias as $tipo => $nome): ?>
            <button class="categoria-btn" data-categoria="<?php echo $tipo; ?>">
              <i class="fas fa-<?php echo getIconeCategoria($tipo); ?>"></i>
              <?php echo $nome; ?>
            </button>
          <?php endforeach; ?>
        </div>

        <!-- Container dos serviços -->
        <?php foreach ($categorias as $tipo => $nome): ?>
          <div class="servicos-grid" id="<?php echo $tipo; ?>-grid">
            <?php if (!empty($servicos[$tipo])): ?>
              <?php foreach ($servicos[$tipo] as $servico): ?>
                <div class="servico-card" <?php echo !empty($servico['url']) ? 'onclick="window.open(\'redirecionar.php?tipo=servico&id=' . $servico['id'] . '\', \'_blank\')"' : ''; ?>>
                  <?php if ($servico['imagem']): ?>
                    <div class="servico-imagem">
                      <img src="../admin/uploads/servicos/<?php echo htmlspecialchars($servico['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($servico['nome']); ?>"
                        loading="lazy">
                      <?php if (!empty($servico['url'])): ?>
                        <div class="link-indicator">
                          <i class="fas fa-external-link-alt"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <div class="servico-info">
                    <h3><?php echo htmlspecialchars($servico['nome']); ?></h3>
                    <p class="endereco">
                      <i class="fas fa-map-marker-alt"></i>
                      <?php echo htmlspecialchars($servico['endereco']); ?>
                    </p>
                    <p class="telefone">
                      <i class="fas fa-phone"></i>
                      <?php echo htmlspecialchars($servico['telefone']); ?>
                    </p>
                    <?php if ($servico['descricao']): ?>
                      <p class="descricao"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="no-content">Nenhum serviço cadastrado nesta categoria.</p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>


    <section class="galeria-section section-spacing" id="galeria">
       <div class="section-title">
        <h2><i class="fas fa-images"></i> Galeria de Destaques</h2>
        <p>Explore nossa galeria e descubra o que temos a oferecer</p>
      </div>
    <div class="galeria-grid">
  <?php if (!empty($galeria_servicos)): ?>
    <?php foreach ($galeria_servicos as $servico): ?>
      <?php if (!empty($servico['url'])): ?>
        <a href="redirecionar.php?tipo=galeria-servicos&id=<?php echo $servico['id']; ?>"
           target="_blank"
           class="galeria-item">
      <?php else: ?>
        <div class="galeria-item">
      <?php endif; ?>
          <div class="galeria-imagem">
            <img src="../admin/uploads/galerias/<?php echo htmlspecialchars($servico['imagem']); ?>"
                alt="<?php echo htmlspecialchars($servico['titulo']); ?>"
                loading="lazy">
            <?php if (!empty($servico['url'])): ?>
              <div class="link-indicator">
                <i class="fas fa-external-link-alt"></i>
              </div>
            <?php endif; ?>
          </div>
          <div class="galeria-info">
            <h3><?php echo htmlspecialchars($servico['titulo']); ?></h3>
            <?php if (!empty($servico['descricao'])): ?>
              <p><?php echo htmlspecialchars($servico['descricao']); ?></p>
            <?php endif; ?>
          </div>
      <?php if (!empty($servico['url'])): ?>
        </a>
      <?php else: ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="no-content">
      <p>Nenhum serviço cadastrado no momento.</p>
    </div>
  <?php endif; ?>
</div>
    </section>


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
            <a href="https://wa.me/5554981222284" target="_blank">
              <i class="fab fa-whatsapp"></i>
              <span>(54) 98122-2284 (Carla)</span>
            </a>
          </li>
          <li class="footer-item">
            <a href="mailto:contato@exemplo.com">
              <i class="fas fa-envelope"></i>
              <span>contato@exemplo.com</span>
            </a>
          </li>
          <li class="footer-item">
            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode('R. Guerino Zugno, 17 - Samuara, Caxias do Sul - RS, 95180-000'); ?>"
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
          <a href="https://www.facebook.com/share/16E44oNUUC/" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
          <a href="https://www.instagram.com/caxiastemturismo?igsh=NTNnbWViZGdybm43" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" target="_blank" title="TikTok"><i class="fab fa-tiktok"></i></a>
          <a href="#" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
        <p class="social-desc">Siga nossas redes sociais e fique por dentro de todas as novidades</p>
      </div>
    </div>
    <!-- Adicione dentro da div footer-bottom nas duas páginas -->
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> Portal de Turismo. Todos os direitos reservados.</p>
      <p class="contador-visitas">
        <i class="fas fa-chart-line"></i>
        Total de visitas: <?php echo number_format($totalVisitas, 0, ',', '.'); ?>
      </p>
    </div>
</footer>

<!-- Scripts -->
<script src="js/calendario.js"></script>
<script src="js/apoiadores.js"></script>
<script src="js/menu.js"></script>
<script src="js/contato.js"></script>
<script src="js/galerias.js"> </script>
<script src="js/serviços.js"></script>
</body>

</html>
