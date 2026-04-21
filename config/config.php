<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'ecommerce',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
        'timezone' => '-04:00',  // UTC-4 para Bolivia (puedes cambiar el offset según tu zona horaria)
    ],
    'app' => [
        'name' => 'Tienda Amiga',
        'url'  => 'http://192.168.26.5/Tecnologias-Web-2/',
        'timezone' => 'America/La_Paz',  // Para PHP
    ]
];
