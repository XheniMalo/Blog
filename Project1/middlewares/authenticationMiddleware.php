<?php
class AuthenticationMiddleware
{
    public function handle($request, $next)
    {
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        
      
        $userRole = $_SESSION['role'];

       
        $adminPages = [
            '/Project1/dashboard', 
            '/Project1/admin',  
            '/Project1/create', 
            '/Project1/createUser', 
            '/Project1/deleteUser'
        ];

        $userPages = [
            '/Project1/homepage', 
            '/Project1/profile', 
            '/Project1/password', 
            '/Project1/post'
        ];

        $uri = parse_url($request, PHP_URL_PATH);

        if (in_array($uri, $adminPages) && $userRole !== 1) {
            http_response_code(403); 
            echo "Access Denied: You don't have permission to access this page.";
            header("Location: /Project1/homepage");
            exit();
        }

        if (in_array($uri, $userPages) && $userRole === 1) {
            http_response_code(403); 
            echo "Access Denied: Admins cannot access this page.";
            header("Location: /Project1/dashboard");
            exit();
        }

        return $next($request);
    }


}
?>