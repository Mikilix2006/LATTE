<?php
// Gestión de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoload de Composer (un nivel arriba de public)
require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

// Iniciar sesión
session_start();

// Configuración de Latte
$latte = new Engine;
$tempDir = __DIR__ . '/../temp';
if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
$latte->setTempDirectory($tempDir);

/**
 * Función global para leer el archivo JSON
 */
function leerDatosMundial() {
    $path = __DIR__ . '/../data/motociclismo.json';
    if (!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true);
}

// IMPORTANTE: Incluir el archivo de rutas
// Pasamos $latte al archivo de rutas mediante 'use' en los closures o globalmente
require __DIR__ . '/../app/config/routes.php';

// Arrancar el framework
Flight::start();

?>