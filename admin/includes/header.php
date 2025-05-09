<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Seu CSS -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Bootstrap Bundle com Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3>Painel Admin</h3>
            </div>
            <ul class="menu">
                <li><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/apoiadores">Apoiadores</a></li>
                <li><a href="<?php echo BASE_URL; ?>/comentarios">Comentários</a></li>
                <li><a href="<?php echo BASE_URL; ?>/contatos">Contatos</a></li>
                <li><a href="<?php echo BASE_URL; ?>/eventos/"><i class="fas fa-home"></i><span>Eventos</span></a></li>
                <li class="has-submenu">
                    <a href="#">Galerias</a>
                    <ul class="submenu">
                        <li><a href="<?php echo BASE_URL; ?>/galerias/home.php">Galeria Home</a></li>
                        <!-- Atualize esta linha -->
                        <li><a href="<?php echo BASE_URL; ?>/galerias/admin_servicos.php">
                                <i class="fas fa-concierge-bell"></i> Galeria Serviços
                            </a></li>
                    </ul>
                </li>
                <li><a href="<?php echo BASE_URL; ?>/servicos">Serviços</a></li>
                <li class="has-submenu">
                    <a href="#">
                        <i class="fas fa-chart-bar"></i>
                        <span>Estatísticas</span>
                    </a>
                    <ul class="submenu">
                        <li><a href="<?php echo BASE_URL; ?>/estatisticas/">Visitas</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/estatisticas/rastreamento.php">Rastreamento</a></li>
                    </ul>
                </li>

                <li><a href="<?php echo BASE_URL; ?>/logout.php">Sair</a></li>
            </ul>
        </nav>
        <div class="content">
