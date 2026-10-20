<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
        $consulta->execute(['email' => $email]);

        if ($consulta->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $insercao = $pdo->prepare(
                'INSERT INTO usuarios (nome, email, senha, perfil)
                 VALUES (:nome, :email, :senha, 0)'
            );

            $insercao->execute([
                'nome'  => $nome,
                'email' => $email,
                'senha' => $senhaHash
            ]);

            $sucesso = 'Usuário cadastrado com sucesso!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Sistema Catálogo</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 420px;">

        <h1 class="h3 text-center mb-2">Sistema Catálogo</h1>

        <p class="text-secondary text-center mb-4">Cadastre sua conta</p>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($sucesso !== ''): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input
                    type="text"
                    class="form-control"
                    id="nome"
                    name="nome"
                    required
                    autocomplete="name"
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <input
                    type="password"
                    class="form-control"
                    id="senha"
                    name="senha"
                    required
                    autocomplete="new-password"
                >
            </div>        

            <button type="submit" class="btn btn-primary w-100">
                Cadastrar
            </button>

            <a href="login.php" class="btn btn-primary w-100 mt-2">
                Login
            </a>
        </form>

    </div>
</div>

</body>
</html>