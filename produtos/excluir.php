excluir.php


<?php
require_once __DIR__ . '/../includes/autenticacao.php';
exigirLogin();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
validarTokenCsrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: index.php?erro=id_invalido');
    exit;
}

try {
    $exclusao = $pdo->prepare(
        'DELETE FROM produtos
         WHERE id = :id
         AND usuario_id = :usuario_id'
    );

    $exclusao->execute([
        'id' => $id,
        'usuario_id' => $_SESSION['usuario_id']]);

    if ($exclusao->rowCount() > 0) {
        header('Location: index.php?sucesso=excluido');
    } else {
        header('Location: index.php?erro=nao_encontrado');
    }

    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());

    header('Location: index.php?erro=exclusao');
    exit;
}
