<?php

require_once __DIR__ . '/config/conexao.php';

$nome = 'Administrador';
$email = 'admin@catalogo.local';
$senha = 'Admin@12345';

try {
    $consulta = $pdo->prepare(
        'SELECT id FROM usuarios WHERE email = :email'
    );

    $consulta->execute(['email' => $email]);

    if ($consulta->fetch()) {
        exit('O usuário inicial já existe. Nenhuma alteração foi feita.');
    }

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

    echo 'Usuário inicial criado com sucesso!';

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Não foi possível criar o usuário inicial.');
}