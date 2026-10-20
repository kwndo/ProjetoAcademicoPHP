
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function exigirLogin(): void
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /sistema-catalogo/login.php');
        exit;
    }
}

function exigirAdmin(): void
{
    exigirLogin();

    if (($_SESSION['usuario_perfil'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Acesso negado. Você não tem permissão para gerenciar usuários.');
    }
}
