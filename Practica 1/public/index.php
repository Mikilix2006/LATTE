<?php
require '../vendor/autoload.php';

use Latte\Engine;

Flight::register('view', Engine::class, [], function ($latte) {
   $latte->setTempDirectory(__DIR__ . '/../cache/');
   $latte->setLoader(new \Latte\Loaders\FileLoader(__DIR__ . '/../app/views/'));
});

Flight::map('posts', function () {
   $file = __DIR__ . '/../data/posts.json';
   return json_decode(file_get_contents($file), true);
});

Flight::map('notFound', function () {
    Flight::view()->render('404.latte', ['title' => 'Página No Encontrada']);
});

require '../app/config/routes.php';

Flight::start();

?>