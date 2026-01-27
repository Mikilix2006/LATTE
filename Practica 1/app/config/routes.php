<?php

Flight::before('start', function(&$params, &$output) {
    $url = Flight::request()->url;
    // Si no hay sesión y no va al login/auth, redirigir
    if (!isset($_SESSION['usuario_id']) && $url !== '/login' && $url !== '/auth') {
        Flight::redirect('/login');
        exit;
    }
});

// --- RUTA: LOGIN ---
Flight::route('/login', function() use ($latte) {
    if (isset($_SESSION['usuario_id'])) Flight::redirect('/');
    
    $latte->render(__DIR__ . '/../views/login.latte', [
        'error' => $_SESSION['error_login'] ?? null
    ]);
    unset($_SESSION['error_login']);
});

// --- RUTA: PROCESAR AUTH ---
Flight::route('POST /auth', function() {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

   $_SESSION['usuario_id'] = $user;
   Flight::redirect('/');
});

// --- RUTA: LOGOUT ---
Flight::route('/logout', function() {
    session_destroy();
    Flight::redirect('/login');
});

// --- RUTA: PRINCIPAL (Búsqueda) ---
Flight::route('/', function() use ($latte) {
    $datos = leerDatosMundial();
    $busqueda = Flight::request()->query->q;
    $resultados = $datos;

    if ($busqueda) {
        $q = strtolower($busqueda);
        $resultados = [];
        foreach ($datos as $cat => $equipos) {
            foreach ($equipos as $equipo => $pilotos) {
                foreach ($pilotos as $piloto) {
                    if (str_contains(strtolower($cat), $q) || 
                        str_contains(strtolower($equipo), $q) || 
                        str_contains(strtolower($piloto), $q)) {
                        $resultados[$cat][$equipo][] = $piloto;
                    }
                }
            }
        }
    }

    $latte->render(__DIR__ . '/../views/index.latte', [
        'usuario' => $_SESSION['usuario_id'],
        'datos' => $resultados,
        'busqueda' => $busqueda
    ]);
});

// --- RUTA: DASHBOARD (Inserción) ---
Flight::route('/dashboard', function() use ($latte) {
    $latte->render(__DIR__ . '/../views/dashboard.latte', [
        'usuario' => $_SESSION['usuario_id'],
        'errores' => $_SESSION['errores_form'] ?? [],
        'equipo' => $_SESSION['equipo'] ?? '',
        'piloto' => $_SESSION['piloto'] ?? ''
    ]);
    unset($_SESSION['errores_form']);
    unset($_SESSION['equipo']);
    unset($_SESSION['piloto']);
});

// --- RUTA: ACCIÓN INSERTAR ---
Flight::route('POST /insertar', function() {
    $datos = leerDatosMundial();
    $cat = $_POST['categoria'] ?? '';
    $equipo = $_POST['equipo'] ?? '';
    $piloto = $_POST['piloto'] ?? '';
    $errores = [];

    // Validaciones
    if (!$cat || !$equipo || !$piloto) $errores[] = "Todos los campos son obligatorios.";
    if (preg_match('/[0-9]/', $piloto)) $errores[] = "El nombre del piloto no puede contener números.";
    if (strlen($piloto) < 5) $errores[] = "Nombre demasiado corto (mín. 5 caracteres).";
    if (isset($datos[$cat][$equipo])) {
        if (in_array($piloto, $datos[$cat][$equipo])) {
            $errores[] = "El piloto '$piloto' ya existe en la escudería '$equipo' de la categoría '$cat'.";
        }
    }
                

    if (empty($errores)) {
        $datos[$cat][$equipo][] = $piloto;
        file_put_contents(__DIR__ . '/../../data/motociclismo.json', json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Flight::redirect('/');
    } else {
        $_SESSION['errores_form'] = $errores;
        $_SESSION['equipo'] = $equipo;
        $_SESSION['piloto'] = $piloto;
        Flight::redirect('/dashboard');
    }
});

?>
