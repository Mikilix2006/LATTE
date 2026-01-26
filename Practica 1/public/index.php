<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use Latte\Engine;

$latte = new Engine;
$tempDir = __DIR__ . '/../temp';
if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
$latte->setTempDirectory($tempDir);

session_start();

// --- FUNCIONES DE APOYO ---
function leerJSON() {
    $path = __DIR__ . '/../data/motociclismo.json';
    return json_decode(file_get_contents($path), true);
}

// Middleware de autenticación: Si no hay sesión, redirige al login
// (Excepto si ya está en la página de login)
Flight::before('start', function(&$params, &$output) {
    $url = Flight::request()->url;
    if (!isset($_SESSION['usuario_id']) && $url !== '/login' && $url !== '/auth') {
        Flight::redirect('/login');
        exit;
    }
});

// --- RUTAS DE AUTENTICACIÓN ---

// Mostrar formulario de Login
Flight::route('/login', function() use ($latte) {
    if (isset($_SESSION['usuario_id'])) Flight::redirect('/');
    
    $latte->render(__DIR__ . '/../app/views/login.latte', [
        'error' => $_SESSION['error_login'] ?? null
    ]);
    unset($_SESSION['error_login']);
});

// Procesar el Login
Flight::route('POST /auth', function() {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    // Validación simple (puedes cambiarla por lo que necesites)
   $_SESSION['usuario_id'] = $user;
   Flight::redirect('/');
});

// Cerrar Sesión
Flight::route('/logout', function() {
    session_destroy();
    Flight::redirect('/login');
});

// --- RUTAS DE LA APLICACIÓN (Protegidas) ---

Flight::route('/', function() use ($latte) {
    $datos = leerJSON();
    $q = Flight::request()->query->q;
    $resultados = $datos;

    if ($q) {
        $q = strtolower($q);
        $resultados = [];
        foreach ($datos as $cat => $equipos) {
            foreach ($equipos as $equipo => $pilotos) {
                foreach ($pilotos as $piloto) {
                    if (str_contains(strtolower($cat), $q) || str_contains(strtolower($equipo), $q) || str_contains(strtolower($piloto), $q)) {
                        $resultados[$cat][$equipo][] = $piloto;
                    }
                }
            }
        }
    }

    $latte->render(__DIR__ . '/../app/views/index.latte', [
        'usuario' => $_SESSION['usuario_id'],
        'datos' => $resultados,
        'busqueda' => $q
    ]);
});

Flight::route('/dashboard', function() use ($latte) {
    $latte->render(__DIR__ . '/../app/views/dashboard.latte', [
        'usuario' => $_SESSION['usuario_id'],
        'errores' => $_SESSION['errores'] ?? []
    ]);
    unset($_SESSION['errores']);
});

Flight::route('POST /insertar', function() {
    $datos = leerJSON();
    $cat = $_POST['categoria'] ?? '';
    $equipo = $_POST['equipo'] ?? '';
    $piloto = $_POST['piloto'] ?? '';
    $errores = [];

    if (!$cat || !$equipo || !$piloto) $errores[] = "Campos vacíos";
    if (preg_match('/[0-9]/', $piloto)) $errores[] = "No números en el nombre";
    
    if (empty($errores)) {
        $datos[$cat][$equipo][] = $piloto;
        file_put_contents(__DIR__ . '/../data/motociclismo.json', json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Flight::redirect('/');
    } else {
        $_SESSION['errores'] = $errores;
        Flight::redirect('/dashboard');
    }
});

Flight::start();

?>