
<?php

session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $consulta = $pdo->prepare(
            'SELECT id, nome, email, senha, perfil
             FROM usuarios
             WHERE email = :email' 
        );

        $consulta->execute(['email' => $email]);
        $usuario = $consulta->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];

            header('Location: dashboard.php');
            exit;
        }

        $erro = 'E-mail ou senha inválidos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema Catálogo</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container min-vh-100 d-flex
            align-items-center justify-content-center">

    <div class="card shadow-sm border-0 p-4"
         style="width: 100%; max-width: 420px;">

        <h1 class="h3 text-center mb-2">
            Sistema Catálogo
        </h1>

        <p class="text-secondary text-center mb-4">
            Entre com sua conta
        </p>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label">
                    E-mail
                </label>

                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">
                    Senha
                </label>

                <input
                    type="password"
                    class="form-control"
                    id="senha"
                    name="senha"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Entrar
            </button>

            <a href="cadastro.php" class="btn btn-outline-primary w-100 mt-2">Cadastrar</a>
        </form>

    </div>
</div>

</body>
</html>
