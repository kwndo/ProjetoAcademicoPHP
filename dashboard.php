
<?php

require_once __DIR__ . '/includes/autenticacao.php';

exigirLogin();

$nome = $_SESSION['usuario_nome'] ?? 'Usuário';

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Sistema Catálogo</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Sistema Catálogo</span>

        <a href="logout.php" class="btn btn-danger">
            Sair
        </a>
    </div>
</nav>

<main class="container py-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h1 class="h3">
                Bem-vindo, <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>!
            </h1>

            <p class="text-secondary mb-0">
                Você entrou no sistema com sucesso.
            </p>
        </div>
    </div>
    
<div class="row g-3 mt-3">
    <div class="col-12 col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="h5">Usuários</h2>
                <p>Gerencie os usuários cadastrados no sistema.</p>
                <a href="usuarios/index.php" class="btn btn-primary">
                    Gerenciar usuários
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h2 class="h5">Produtos e serviços</h2>
                <p>Cadastre, consulte, edite e exclua itens do catálogo.</p>
                <a href="produtos/index.php" class="btn btn-primary">
                    Gerenciar catálogo
                </a>
            </div>
        </div>
    </div>
</div>

</main>

</body>
</html>
