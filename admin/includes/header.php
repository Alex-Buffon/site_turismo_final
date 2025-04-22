<?php
session_start();
require_once __DIR__ . '/conexao.php';

if(!isset($_SESSION['admin_logado'])) {
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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
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
                        <li><a href="<?php echo BASE_URL; ?>/galerias/servicos.php">Galeria Serviços</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo BASE_URL; ?>/servicos">Serviços</a></li>
                <li><a href="<?php echo BASE_URL; ?>/logout.php">Sair</a></li>
            </ul>
        </nav>
        <div class="content">
