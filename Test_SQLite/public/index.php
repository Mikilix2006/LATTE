<?php
require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../config/config.php';

Flight::route('/', function(){
    echo 'Hello, world!';
});

Flight::register('db', \flight\database\PdoWrapper::class, [ 'sqlite:'.$config['database_path'] ], function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
});

if(file_exists($config['database_path']) === false) {
    $db = Flight::db();
    $db->runQuery("CREATE TABLE users (id INTEGER PRIMARY KEY, 
                                        name TEXT NOT NULL, 
                                        email TEXT NOT NULL, 
                                        password TEXT NOT NULL)");
} //else {
    //$db = Flight::db();
    //$db->runQuery("INSERT INTO users (id, name, email, password) VALUES (1,'Alice', 'alice1@example.com','alice1')");
    //$db->runQuery("INSERT INTO users (id, name, email, password) VALUES (2,'Rosa', 'rosa1@example.com','rosa1')");
    //$db->runQuery("INSERT INTO users (id, name, email, password) VALUES (3,'Pedro', 'pedro1@example.com','pedro1')");
    //$db->runQuery("INSERT INTO users (id, name, email, password) VALUES (5,'Alice', 'alice1@example.com','alice1')");
    //echo "inserción ok";
//}

// A group helps group together similar routes for convenience
// bash: curl -H "Content-Type: application/json" http://localhost:8000/users
// bash: curl -H "Content-Type: application/json" http://localhost:8000/users/@id
Flight::group('/users', function(\flight\net\Router $router) {
// Get all users
    $router->get('', function(){
        $db = Flight::db();
        $users = $db->fetchAll("SELECT * FROM users");
        Flight::json($users);
    });  
// Get user by id
    $router->get('/@id', function($id){
        $db = Flight::db();
        $user = $db->fetchRow("SELECT * FROM users WHERE id = :id", [ ':id' => $id ]);
        if (!empty($user['id'])) {
            Flight::json($user);
        } else {
            Flight::jsonHalt([ 'message' => 'User not found' ], 404);
        }
    });

});

// RA9.c) Recuperación y procesamiento de repositorios de información ya existente
// RA9.d)Creación de repositorio específicos a partir de información existente en almacenes de información
// Probar con http://localhost:8080/productos-procesados

Flight::route('GET /productos-procesados', function() {
    // 1. Obtener los productos de Fake Store API
    $url = 'https://fakestoreapi.com/products';
    $response = file_get_contents($url); // Método simple
    
    if ($response === false) {
        Flight::json(['error' => 'No se pudo conectar con la API externa'], 500);
        return;
    }

    // 2. Decodificar el JSON a un array de PHP
    $productos = json_decode($response, true);

    // 3. Procesar los datos (Ejemplo: filtrar por precio o formatear nombres)
    $productosProcesados = array_map(function($item) {
        return [
            'id' => $item['id'],
            'nombre' => strtoupper($item['title']), // Transformación simple
            'precio_eur' => $item['price'] * 0.92,   // Conversión ficticia
            'categoria' => $item['category']
        ];
    }, $productos);

    // 4. Creación de repositorio Productos en BD SQLite

    // 5. Devolver la respuesta procesada en formato JSON
    Flight::json($productosProcesados);

});

// RA9.f) Programación de servicios y aplicaciones web utilizando información y código generado por terceros
// Por ejemplo: Con la información obtenida de esta pokeapi se podría programar un servicio que devolviera determinada información del pokemon
// Probar con http://localhost:8080/pokemon/ditto

Flight::route('/pokemon/@nombre', function($nombre) {
    // 1. Corregir URL: Debe incluir /api/v2/pokemon/
    $url = "https://pokeapi.co/api/v2/pokemon/" . strtolower($nombre);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 2. Recomendado: Seguir redirecciones y establecer un User-Agent
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'FlightPHP-PokeApp/1.0');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        // 3. Decodificar como objeto para que Flight::json lo vuelva a codificar correctamente
        Flight::json(json_decode($response));
    } else {
        // 4. Manejo de error con el código de estado adecuado
        Flight::json(['error' => 'Pokémon no encontrado'], 404);
    }
});


/* 
 RA9.e) utilización de librerías de código y frameworks para incorporar funcionalidades de análisis o IA
 RA9.e) análisis y utilización de librerías de código relacionadas con Big Data e
 inteligencia de negocios, para incorporar análisis e inteligencia de datos proveniente de
 repositorios.
 Ejemplo de análisis estadístico y predictivo con PHP-ML
 CREATE TABLE metricas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repo_id INT NOT NULL,            -- El identificador que pasas en la ruta @id
    valor DECIMAL(10, 2) NOT NULL,    -- Los números para calcular media, mediana, etc.
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
Insertar:
INSERT INTO metricas (repo_id, valor) VALUES 
(1, 10.5), (1, 20.0), (1, 15.75), (1, 30.2), (1, 18.5);


use Phpml\Math\Statistic\Mean;
use Phpml\Math\Statistic\Correlation;
use Phpml\Math\Statistic\StandardDeviation;


Flight::route('GET /stats/@id_repositorio', function($id_repositorio) {
    // 1. Obtener datos de tu repositorio (DB, CSV o API)
    $datos = Flight::db()->query("SELECT valor FROM metricas WHERE repo_id = $id_repositorio")->fetchAll(PDO::FETCH_COLUMN);

    if (empty($datos)) {
        Flight::json(['error' => 'No hay datos'], 404);
        return;
    }

    // 2. Realizar cálculos estadísticos con PHP-ML
    $analisis = [
        'media' => Mean::arithmetic($datos), // Calcula el promedio
        'mediana' => Mean::median($datos), // Calcula el valor central
        'desviacion_estandar' => StandardDeviation::population($datos), // Dispersión
    ];

    // 3. Opcional: Correlación entre dos variables
    // $x = [1, 2, 3]; $y = [2, 4, 6];
    // $analisis['correlacion_pearson'] = Correlation::pearson($x, $y);

    Flight::json($analisis);
});

*/

//API REST
Flight::group('/users', function(\flight\net\Router $router) {

// Get all users
    $router->get('', function(){
        $db = Flight::db();
        $users = $db->fetchAll("SELECT * FROM users");
        Flight::json($users);
    });  


// Get user by id
    $router->get('/@id', function($id){
        $db = Flight::db();
        $user = $db->fetchRow("SELECT * FROM users WHERE id = :id", [ ':id' => $id ]);
        if (!empty($user['id'])) {
            Flight::json($user);
        } else {
            Flight::jsonHalt([ 'message' => 'User not found' ], 404);
        }
    });

/* Create new user 
Probar desde el directorio del proyecto en el terminal: 
curl -X POST -H "Content-Type: application/json" -d "{\"name\":\"Doe\",\"email\":\"doe@example.com\",\"password\":\"password\"}" http://localhost:8080/users
C:\Users\madrid\Desktop\flight-api>curl -X POST -H "Content-Type: application/json" -d "{\"name\":\"Doe\",\"email\":\"doe@example.com\",\"password\":\"password\"}" http://localhost:8080/users
Salida:
{"id":"6"}
*/
    $router->post('', function(){
        $data = Flight::request()->data;
        $db = Flight::db();
        $result = $db->runQuery("INSERT INTO users (id, name, email, password) VALUES (:id, :name, :email, :password)", [     
            ':id' => $data['id'],
            ':name' => $data['name'], 
            ':email' => $data['email'], 
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT) 
        ]);
        Flight::json([ 'id' => $db->lastInsertId() ], 201);
    });

});

Flight::start();
