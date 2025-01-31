<?php
class LoginMiddleware
{
    public function handle($request, $next)
    {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $publicRoutes = [
                        '/Project1/login', 
                        '/Project1/register'
        ];

        if (!isset($_SESSION['loggedin']) && !in_array($request, $publicRoutes)) {
            header("Location: /Project1/login");
            exit();
        }

        return $next($request);

    }
}
?>