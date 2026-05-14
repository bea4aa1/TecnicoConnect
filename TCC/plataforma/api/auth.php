<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/router.php';
setCors();
if (session_status() === PHP_SESSION_NONE) session_start();
$action = param('action');

// LOGIN
if ($action === 'login' && isPost()) {
    $b     = bodyJson();
    $email = trim($b['email'] ?? '');
    $senha = $b['senha'] ?? '';
    if (!$email || !$senha) jsonOut(['erro' => 'Informe e-mail e senha.'], 400);
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE email=? AND ativo=1 LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($senha, $user['senha'])) jsonOut(['erro' => 'E-mail ou senha incorretos.'], 401);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_nome'] = $user['nome'];
    $_SESSION['user_tipo'] = $user['tipo'];
    unset($user['senha']);
    jsonOut(['ok' => true, 'user' => $user]);
}

// CADASTRO
if ($action === 'register' && isPost()) {
    $b     = bodyJson();
    $nome  = clean($b['nome']  ?? '');
    $email = trim($b['email']  ?? '');
    $senha = $b['senha'] ?? '';
    $tipo  = $b['tipo'] === 'empresa' ? 'empresa' : 'dev';
    $espec = clean($b['espec'] ?? '');
    $nivel = clean($b['nivel'] ?? '');
    if (!$nome || !$email || !$senha)           jsonOut(['erro' => 'Preencha todos os campos.'], 400);
    if (strlen($senha) < 6)                     jsonOut(['erro' => 'Senha muito curta (mín. 6).'], 400);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonOut(['erro' => 'E-mail inválido.'], 400);
    $db  = getDB();
    $chk = $db->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1');
    $chk->bind_param('s', $email); $chk->execute();
    if ($chk->get_result()->fetch_assoc()) jsonOut(['erro' => 'E-mail já cadastrado.'], 409);
    $hash = password_hash($senha, PASSWORD_BCRYPT);
    $ins  = $db->prepare('INSERT INTO usuarios (nome,email,senha,tipo,especialidade,nivel,email_contato) VALUES (?,?,?,?,?,?,?)');
    $ins->bind_param('sssssss', $nome, $email, $hash, $tipo, $espec, $nivel, $email);
    if (!$ins->execute()) jsonOut(['erro' => 'Erro ao criar conta.'], 500);
    $id = $db->insert_id;
    $_SESSION['user_id']   = $id;
    $_SESSION['user_nome'] = $nome;
    $_SESSION['user_tipo'] = $tipo;
    jsonOut(['ok' => true, 'user' => ['id'=>$id,'nome'=>$nome,'email'=>$email,'tipo'=>$tipo,'especialidade'=>$espec,'nivel'=>$nivel]], 201);
}

// LOGOUT
if ($action === 'logout' && isPost()) { session_destroy(); jsonOut(['ok' => true]); }

// ME
if ($action === 'me' && isGet()) {
    if (empty($_SESSION['user_id'])) jsonOut(['autenticado' => false]);
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM usuarios WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) { session_destroy(); jsonOut(['autenticado' => false]); }
    unset($user['senha']);
    jsonOut(['autenticado' => true, 'user' => $user]);
}

jsonOut(['erro' => 'Ação inválida.'], 400);
