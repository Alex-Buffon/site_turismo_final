<?php
session_start();
require_once 'includes/conexao.php';

// Criar hash da senha para teste
// $senha = 'admin123';
// echo password_hash($senha, PASSWORD_DEFAULT);

if(isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Credenciais fixas para teste
    $admin_user = 'admin';
    $admin_pass = 'admin123';

    if($usuario === $admin_user && $senha === $admin_pass) {
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_id'] = 1;
        header('Location: index.php');
        exit;
    } else {
        $erro = "Usuário ou senha inválidos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <h2>Login Administrativo</h2>

        <?php if(isset($erro)): ?>
            <div class="alert alert-erro"><?php echo $erro; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Usuário:</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>

            <button type="submit" name="login" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</body>
</html>
