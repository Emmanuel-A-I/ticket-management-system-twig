<?php
class Auth {
    private $users;
    
    public function __construct() {
        // Mock users database
        $this->users = [
            [
                'id' => 1,
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'name' => 'Admin User'
            ],
            [
                'id' => 2, 
                'email' => 'user@example.com',
                'password' => 'user123',
                'name' => 'Regular User'
            ]
        ];
    }
    
    public function login($email, $password) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email && $user['password'] === $password) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name']
                ];
                $_SESSION['token'] = bin2hex(random_bytes(32));
                return true;
            }
        }
        return false;
    }
    
    public function isAuthenticated() {
        return isset($_SESSION['user']) && isset($_SESSION['token']);
    }
    
    public function getUser() {
        return $_SESSION['user'] ?? null;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function getToken() {
        return $_SESSION['token'] ?? null;
    }
}
?>
