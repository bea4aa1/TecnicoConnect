<?php
require_once __DIR__ . '/db.php';

function requireAuth(): array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) jsonOut(['erro' => 'Não autenticado.'], 401);
    return ['id' => (int)$_SESSION['user_id'], 'tipo' => $_SESSION['user_tipo'] ?? 'dev'];
}
function requireEmpresa(): array {
    $u = requireAuth();
    if ($u['tipo'] !== 'empresa') jsonOut(['erro' => 'Acesso restrito a empresas.'], 403);
    return $u;
}
function requireDev(): array {
    $u = requireAuth();
    if ($u['tipo'] !== 'dev') jsonOut(['erro' => 'Acesso restrito a desenvolvedores.'], 403);
    return $u;
}
