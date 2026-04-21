<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(array $config): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['name'], $config['charset']);
            if (!empty($config['port'])) {
                $dsn .= sprintf(';port=%s', $config['port']);
            }
            self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            // Establecer zona horaria para MySQL
            if (!empty($config['timezone'])) {
                $tz = $config['timezone'];
                self::$instance->exec("SET time_zone = '{$tz}'");
            }
        }
        return self::$instance;
    }
}