
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirAdmin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

$consulta = $pdo->prepare(
    'SELECT id, nome, email FROM usuarios WHERE id = :id'
);
$consulta->execute(['id' => $id]);
$usuario = $consulta->fetch();

if (!$usuario) {
    header('Location: index.php?erro=nao_encontrado');
    exit;
}

$nome = $usuario['nome'];
$email = $usuario['email'];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

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

    if (empty($erros)) {
        try {
            $consultaEmail = $pdo->prepare(
                'SELECT id FROM usuarios
                 WHERE email = :email AND id <> :id'
            );
            $consultaEmail->execute([
                'email' => $email,
                'id' => $id
            ]);

            if ($consultaEmail->fetch()) {
                $erros[] = 'Este e-mail já pertence a outro usuário.';
            } else {
                $atualizacao = $pdo->prepare(
                    'UPDATE usuarios
                     SET nome = :nome, email = :email
                     WHERE id = :id'
                );

                $atualizacao->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'id' => $id
                ]);

                header('Location: index.php?sucesso=editado');
                exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros[] = 'Não foi possível atualizar o usuário.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário</title>

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
                    <h1 class="h3 mb-4">Editar usuário</h1>

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li><?= htmlspecialchars(
                                        $erro,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="editar.php?id=<?= (int) $id ?>">
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
                            <label for="nome" class="form-label">Nome</label>
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

                        <div class="mb-4">
                            <label for="email" class="form-label">E-mail</label>
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

                        <button type="submit" class="btn btn-primary">
                            Salvar alterações
                        </button>

                        <a href="index.php" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
