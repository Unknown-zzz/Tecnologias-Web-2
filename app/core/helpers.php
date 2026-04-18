<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['usuario']);
}

function isAdmin(): bool
{
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: index.php?r=login');
        exit();
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: index.php?r=admin/login');
        exit();
    }
}