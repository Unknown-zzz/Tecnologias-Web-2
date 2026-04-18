<?php
declare(strict_types=1);

abstract class Controller
{
    protected PDO $db;
    protected array $config;

    public function __construct(PDO $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }

    protected function redirect(string $route): void
    {
        header('Location: index.php?r=' . $route);
        exit();
    }
}