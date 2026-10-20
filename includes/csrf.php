
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function gerarTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validarTokenCsrf(): void
{
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($tokenEnviado)
        || !is_string($tokenSessao)
        || $tokenEnviado === ''
        || !hash_equals($tokenSessao, $tokenEnviado)
    ) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página e tente novamente.');
    }
}