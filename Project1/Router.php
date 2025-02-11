<?php
require_once __DIR__ . '/Models/Database.php'; 
class Router
{
    private $conn;
    protected static $middlewares=[];

    public static function addMiddleware($middleware)
    {
        if (class_exists($middleware)) {
            self::$middlewares[] = new $middleware();
        } else {
            throw new Exception("Middleware class '$middleware' not found.");
        }
    }
    public static function route($method, $uri)
    {
        $next = function ($request) use ($method, $uri) {
        $routes = [
            ['uri' => '/Project1/login', 'request' => 'GET', 'controller' => 'AuthenticationController', 'method' => 'showLogin'],
            ['uri' => '/Project1/login', 'request' => 'POST', 'controller' => 'AuthenticationController', 'method' => 'login'],
            ['uri' => '/Project1/register', 'request' => 'GET', 'controller' => 'AuthenticationController', 'method' => 'showRegistrationForm'],
            ['uri' => '/Project1/register', 'request' => 'POST', 'controller' => 'AuthenticationController', 'method' => 'register'],
            ['uri' => '/Project1/logout', 'request' => 'GET', 'controller' => 'AuthenticationController', 'method' => 'logout'],
            ['uri' => '/Project1/homepage', 'request' => 'GET', 'controller' => 'UserController', 'method' => 'showHomepage'],
            ['uri' => '/Project1/homepage', 'request' => 'POST', 'controller' => 'UserController', 'method' => 'homepage'],
            ['uri' => '/Project1/profile', 'request' => 'GET', 'controller' => 'UserController', 'method' => 'profile'],
            ['uri' => '/Project1/profile', 'request' => 'POST', 'controller' => 'UserController', 'method' => 'updateProfile'],
            ['uri' => '/Project1/profilepic', 'request' => 'POST', 'controller' => 'UserController', 'method' => 'updateProfilePicture'],
            ['uri' => '/Project1/password', 'request' => 'GET', 'controller' => 'UserController', 'method' => 'password'],
            ['uri' => '/Project1/password', 'request' => 'POST', 'controller' => 'UserController', 'method' => 'changePassword'],
            ['uri' => '/Project1/post', 'request' => 'GET', 'controller' => 'PostsController', 'method' => 'post'],
            ['uri' => '/Project1/post', 'request' => 'POST', 'controller' => 'PostsController', 'method' => 'addPost'],
            ['uri' => '/Project1/delete', 'request' => 'POST', 'controller' => 'PostsController', 'method' => 'deletePost'],
            ['uri' => '/Project1/edit', 'request' => 'POST', 'controller' => 'PostsController', 'method' => 'showedit'],
            ['uri' => '/Project1/editPost', 'request' => 'POST', 'controller' => 'PostsController', 'method' => 'editPost'],
            ['uri' => '/Project1/deleteImage', 'request' => 'POST', 'controller' => 'PostsController', 'method' => 'deleteImage'],
            ['uri' => '/Project1/dashboard', 'request' => 'GET', 'controller' => 'AdminController', 'method' => 'showDashboard'],
            ['uri' => '/Project1/posts', 'request' => 'POST', 'controller' => 'AdminUserController', 'method' => 'showPosts'],
            ['uri' => '/Project1/deleteUser', 'request' => 'POST', 'controller' => 'AdminUserController', 'method' => 'deleteUser'],
            ['uri' => '/Project1/admin', 'request' => 'GET', 'controller' => 'AdminController', 'method' => 'adminProfile'],
            ['uri' => '/Project1/adminprofile', 'request' => 'POST', 'controller' => 'AdminController', 'method' => 'updateProfile'],
            ['uri' => '/Project1/create', 'request' => 'GET', 'controller' => 'AdminUserController', 'method' => 'showCreate'],
            ['uri' => '/Project1/createUser', 'request' => 'POST', 'controller' => 'AdminUserController', 'method' => 'createNewUser'],
            ['uri' => '/Project1/passwordadmin', 'request' => 'GET', 'controller' => 'AdminController', 'method' => 'password'],
            ['uri' => '/Project1/passwordadmin', 'request' => 'POST', 'controller' => 'AdminController', 'method' => 'changePassword'],
            ['uri' => '/Project1/adminpic', 'request' => 'POST', 'controller' => 'AdminController', 'method' => 'updateProfilePicture'],
            ['uri' => '/Project1/userEdit', 'request' => 'POST', 'controller' => 'AdminUserController', 'method' => 'showeditUser'],
            ['uri' => '/Project1/userEditing', 'request' => 'POST', 'controller' => 'AdminUserController', 'method' => 'editUser'],
            ['uri'=> '/Project1/adminedits', 'request'=> 'GET', 'controller' => 'AdminUserController', 'method'=> 'editPost'],
            ['uri'=> '/Project1/admindelete', 'request'=> 'POST', 'controller' => 'AdminUserController', 'method'=> 'deletePost'],

        ];
        

        foreach ($routes as $route) {
            if ($route['uri'] === $uri && $route['request'] === $method) {
                $controllerName = $route['controller'];
                $controllerMethod = $route['method'];

                include_once __DIR__ . "/controller/{$controllerName}.php";
                // echo "Loading controller: {$controllerName}, method: {$controllerMethod}\n";  

                $database = new Database();
                $conn = $database->getConnection();

                $controller = new $controllerName($conn);
                $controller->$controllerMethod($_POST ?? []);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";

        exit();
    };
    
    foreach (self::$middlewares as $middleware) {
        $next = function ($request) use ($middleware, $next) {
            return $middleware->handle($request, $next);
        };
    }
    $next($uri);
    
}
}
?>