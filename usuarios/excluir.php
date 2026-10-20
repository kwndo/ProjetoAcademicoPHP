
<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirAdmin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    validarTokenCsrf();
    header('Location: index.php');
    exit;
}

if (!validarTokenCsrf($_POST['token_csrf'] ?? null)) {
    http_response_code(403);
    exit('Solicitação inválida. Atualize a página e tente novamente.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php?erro=id_invalido');
    exit;
}


if ($id === (int) $_SESSION['usuario_id']) {
    header('Location: index.php?erro=propria_conta');
    exit;
}

try {
    $consulta = $pdo->prepare(
        'SELECT id FROM usuarios WHERE id = :id'
    );
    $consulta->execute(['id' => $id]);

    if (!$consulta->fetch()) {
        header('Location: index.php?erro=nao_encontrado');
        exit;
    }

    $exclusao = $pdo->prepare(
        'DELETE FROM usuarios WHERE id = :id'
    );
    $exclusao->execute(['id' => $id]);

    header('Location: index.php?sucesso=excluido');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?erro=exclusao_falhou');
    exit;
}
