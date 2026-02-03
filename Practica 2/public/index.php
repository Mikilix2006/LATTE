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
    $db->runQuery("CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL, email TEXT NOT NULL, password TEXT NOT NULL)");
}

Flight::start();
