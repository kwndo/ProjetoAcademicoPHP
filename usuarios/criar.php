
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirAdmin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

$erros = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '') {
        $erros[] = 'O nome é obrigatório.';
    }

    if (strlen($nome) > 100) {
        $erros[] = 'O nome deve ter no máximo 100 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Informe um e-mail válido.';
    }

    if (strlen($email) > 150) {
        $erros[] = 'O e-mail deve ter no máximo 150 caracteres.';
    }

    if (strlen($senha) < 8) {
        $erros[] = 'A senha deve ter pelo menos 8 caracteres.';
    }

    if ($senha !== $confirmarSenha) {
        $erros[] = 'As senhas não são iguais.';
    }

    if (empty($erros)) {
        try {
            $consulta = $pdo->prepare(
                'SELECT id FROM usuarios WHERE email = :email'
            );
            $consulta->execute(['email' => $email]);

            if ($consulta->fetch()) {
                $erros[] = 'Este e-mail já está cadastrado.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $insercao = $pdo->prepare(
                    'INSERT INTO usuarios (nome, email, senha)
                     VALUES (:nome, :email, :senha)'
                );

                $insercao->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senhaHash
                ]);

                header('Location: index.php?sucesso=cadastrado');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros[] = 'Não foi possível cadastrar o usuário. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Cadastrar usuário</h1>

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li>
                                        <?= htmlspecialchars(
                                            $erro,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="criar.php">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                            gerarTokenCsrf(),
                            ENT_QUOTES,
                            'UTF-8'
                            ) ?>"
                        >
                        <div class="mb-3">
                            <label for="nome" class="form-label">
                                Nome completo
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nome"
                                name="nome"
                                maxlength="100"
                                value="<?= htmlspecialchars(
                                    $nome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                E-mail
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                maxlength="150"
                                value="<?= htmlspecialchars(
                                    $email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">
                                Senha (mínimo de 8 caracteres)
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="senha"
                                name="senha"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="confirmar_senha" class="form-label">
                                Confirmar senha
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="confirmar_senha"
                                name="confirmar_senha"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Cadastrar
                            </button>

                            <a href="index.php" class="btn btn-outline-secondary">
                                Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
