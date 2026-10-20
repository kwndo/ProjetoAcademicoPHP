
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirLogin();

require_once __DIR__ . '/../config/conexao.php';

require_once __DIR__ . '/../includes/csrf.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}

$consulta = $pdo->prepare(
    'SELECT id, nome, descricao, tipo, preco
     FROM produtos
     WHERE id = :id'
);
$consulta->execute(['id' => $id]);
$produto = $consulta->fetch();

if (!$produto) {
    header('Location: index.php?erro=nao_encontrado');
    exit;
}

$nome = $produto['nome'];
$descricao = $produto['descricao'] ?? '';
$tipo = $produto['tipo'];
$preco = (string) $produto['preco'];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarTokenCsrf();
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $preco = trim($_POST['preco'] ?? '');

    if ($nome === '') {
        $erros[] = 'O nome é obrigatório.';
    }

    if (strlen($nome) > 100) {
        $erros[] = 'O nome deve ter no máximo 100 caracteres.';
    }

    if (strlen($descricao) > 5000) {
        $erros[] = 'A descrição deve ter no máximo 5000 caracteres.';
    }

    if (!in_array($tipo, ['Produto', 'Servico'], true)) {
        $erros[] = 'Selecione um tipo válido.';
    }

    $precoNormalizado = str_replace(',', '.', $preco);

    if (
        $preco === ''
        || !is_numeric($precoNormalizado)
        || !is_finite((float) $precoNormalizado)
        || (float) $precoNormalizado < 0
        || (float) $precoNormalizado > 99999999.99
        || !preg_match('/^\d{1,8}(?:[.,]\d{1,2})?$/', $preco)
    ) {
        $erros[] = 'Informe um preço válido, com até duas casas decimais.';
    }

    if (empty($erros)) {
        try {
            $atualizacao = $pdo->prepare(
                'UPDATE produtos
                 SET nome = :nome,
                     descricao = :descricao,
                     tipo = :tipo,
                     preco = :preco
                 WHERE id = :id'
            );

            $atualizacao->execute([
                'nome' => $nome,
                'descricao' => $descricao !== '' ? $descricao : null,
                'tipo' => $tipo,
                'preco' => $precoNormalizado,
                'id' => $id
            ]);

            header('Location: index.php?sucesso=editado');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros[] = 'Não foi possível atualizar o item.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar produto ou serviço</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-7">

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <h1 class="h3 mb-4">Editar produto ou serviço</h1>

                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger" role="alert">
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

                        <div class="mb-3">
                            <label for="descricao" class="form-label">
                                Descrição
                            </label>

                            <textarea
                                class="form-control"
                                id="descricao"
                                name="descricao"
                                rows="4"
                                maxlength="5000"
                            ><?= htmlspecialchars(
                                $descricao,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>

                            <select
                                class="form-select"
                                id="tipo"
                                name="tipo"
                                required
                            >
                                <option
                                    value="Produto"
                                    <?= $tipo === 'Produto' ? 'selected' : '' ?>
                                >
                                    Produto
                                </option>

                                <option
                                    value="Servico"
                                    <?= $tipo === 'Servico' ? 'selected' : '' ?>
                                >
                                    Serviço
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="preco" class="form-label">
                                Preço (R$)
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="preco"
                                name="preco"
                                inputmode="decimal"
                                value="<?= htmlspecialchars(
                                    $preco,
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
