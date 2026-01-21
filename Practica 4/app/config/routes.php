<?php
Flight::route('/', function () {
   $posts = Flight::posts();
   Flight::view()->render('home.latte', [
       'title' => 'Mi Blog',
       'posts' => $posts
   ]);
});

Flight::route('/post/@slug', function ($slug) {
   $posts = Flight::posts();
   $post = array_filter($posts, fn($p) => $p['slug'] === $slug);
   $post = reset($post) ?: null;
   if (!$post) {
       Flight::notFound();
       return;
   }
   Flight::view()->render('post.latte', [
       'title' => $post['title'],
       'post' => $post
   ]);
});

Flight::route('GET /create', function () {
   Flight::view()->render('create.latte', ['title' => 'Crear una Publicación']);
});

Flight::route('POST /create', function () {
   $request = Flight::request();
   $title = $request->data['title'];
   $content = $request->data['content'];
   $slug = strtolower(str_replace(' ', '-', $title));

   $posts = Flight::posts();
   $posts[] = ['slug' => $slug, 'title' => $title, 'content' => $content];
   file_put_contents(__DIR__ . '/../../data/posts.json', json_encode($posts, JSON_PRETTY_PRINT));

   Flight::redirect('/');
});

?>