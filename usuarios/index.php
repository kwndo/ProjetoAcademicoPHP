
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirAdmin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

try {
    $consulta = $pdo->query(
        'SELECT id, nome, email, criado_em
         FROM usuarios
         ORDER BY nome ASC'
    );

    $usuarios = $consulta->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Não foi possível carregar os usuários.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar usuários</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">
    
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="h3 mb-0">Gerenciar usuários</h1>

    <div class="d-flex gap-2">
        <a href="criar.php" class="btn btn-primary">
            Novo usuário
        </a>

        <a href="../dashboard.php" class="btn btn-outline-secondary">
            Voltar ao dashboard
        </a>
    </div>
</div>

<?php if (($_GET['sucesso'] ?? '') === 'cadastrado'): ?>
    <div class="alert alert-success" role="alert">
        Usuário cadastrado com sucesso!
    </div>
<?php endif; ?>

<?php if (($_GET['sucesso'] ?? '') === 'editado'): ?>
    <div class="alert alert-success" role="alert">
        Usuário atualizado com sucesso!
    </div>
<?php endif; ?>
<?php if (($_GET['sucesso'] ?? '') === 'excluido'): ?>
    <div class="alert alert-success" role="alert">
        Usuário excluído com sucesso!
    </div>
<?php endif; ?>
<?php if (($_GET['erro'] ?? '') === 'propria_conta'): ?>
    <div class="alert alert-danger" role="alert">
        Você não pode excluir a própria conta conectada.
    </div>
<?php endif; ?> 

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-3">Usuários cadastrados</h2>

            <?php if (count($usuarios) === 0): ?>
                <p>Nenhum usuário cadastrado.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Data de cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td>
                                        <?= (int) $usuario['id'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $usuario['nome'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $usuario['email'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $usuario['criado_em'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                        <a
                                            href="editar.php?id=<?= (int) $usuario['id'] ?>"
                                            class="btn btn-sm btn-warning"
                                        >
                                            Editar
                                        </a>

                                        <?php if ((int) $usuario['id'] !== (int) $_SESSION['usuario_id']): ?>
                                            <form
                                                action="excluir.php"
                                                method="POST"
                                                onsubmit="return confirm('Tem certeza de que deseja excluir este usuário?');"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $usuario['id'] ?>"
                                                >

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Excluir
                                                </button>

                                                <input
                                                    type="hidden"
                                                    name="token_csrf"
                                                    value="<?= htmlspecialchars(
                                                        gerarTokenCsrf(),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                >
                                             </form>
                                             <?php else: ?>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-secondary"
                                                disabled
                                                title="Não é possível excluir a própria conta conectada."
                                            >
                                                Excluir
                                            </button>

                                             <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
