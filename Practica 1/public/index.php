<?php

require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

// Iniciar sesión
session_start();

// Configuración de Latte
$latte = new Engine;
$tempDir = __DIR__ . '/../temp';
if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
$latte->setTempDirectory($tempDir);

function leerDatosMundial() {
    $path = __DIR__ . '/../data/motociclismo.json';
    if (!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true);
}

// Incluir rutas
require __DIR__ . '/../app/config/routes.php';

Flight::start();

?>