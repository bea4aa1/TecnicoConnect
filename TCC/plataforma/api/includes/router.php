<?php
function setCors(): void {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
}
function method(): string  { return strtoupper($_SERVER['REQUEST_METHOD']); }
function isGet():    bool  { return method() === 'GET'; }
function isPost():   bool  { return method() === 'POST'; }
function isPut():    bool  { return method() === 'PUT'; }
function isDelete(): bool  { return method() === 'DELETE'; }
function param(string $key, mixed $default = null): mixed { return $_GET[$key] ?? $default; }
