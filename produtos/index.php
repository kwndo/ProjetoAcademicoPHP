
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirLogin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

try {
    $consulta = $pdo->query(
        'SELECT id, nome, descricao, preco, criado_em
         FROM produtos
         ORDER BY nome ASC'
    );

    $produtos = $consulta->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Não foi possível carregar os produtos e serviços.');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produtos</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Produtos e serviços</h1>
        <div class="d-flex flex-wrap gap-2">
            <a href="criar.php" class="btn btn-primary">
                Novo item
            </a>

        <a href="../dashboard.php" class="btn btn-outline-secondary">
            Voltar ao dashboard
        </a>
    </div>
</div>

    <div class="card shadow-sm">
        <div class="card-body">

            <h2 class="h5 mb-3">Itens cadastrados</h2>

            <?php if (empty($produtos)): ?>

                <div class="alert alert-info">
                    Nenhum produto cadastrado.
                </div>

            <?php else: ?>
                <?php if (($_GET['sucesso'] ?? '') === 'editado'): ?>
                    <div class="alert alert-success" role="alert">
                        Produto atualizado com sucesso!
                    </div>
                <?php endif; ?>

                 
            <?php if (($_GET['sucesso'] ?? '') === 'excluido'): ?>
                <div class="alert alert-success" role="alert">
                    Produto excluído com sucesso!
                </div>
            <?php endif; ?>

            <?php if (($_GET['erro'] ?? '') === 'id_invalido'): ?>
                <div class="alert alert-danger" role="alert">
                    Identificador inválido.
                </div>
            <?php endif; ?>

            <?php if (($_GET['erro'] ?? '') === 'nao_encontrado'): ?>
                <div class="alert alert-warning" role="alert">
                    O item não foi encontrado.
                </div>
            <?php endif; ?>

            <?php if (($_GET['erro'] ?? '') === 'exclusao'): ?>
                <div class="alert alert-danger" role="alert">
                    Não foi possível excluir o item.
                </div>
            <?php endif; ?>


                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Ações</th>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Preço</th>
                                <th>Data de cadastro</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td>
                                    <form
                                        action="excluir.php"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Tem certeza de que deseja excluir este item?');"
                                        >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                gerarTokenCsrf(),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $produto['id'] ?>"
                                        >

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Excluir
                                    </button>
                                    </form>

                                     <a href="editar.php?id=<?= (int) $produto['id'] ?>"
                                            class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    </td>
                                    <td>
                                        <?= (int) $produto['id'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $produto['nome'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $produto['descricao'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        R$ <?= number_format(
                                            (float) $produto['preco'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $produto['criado_em'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
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
