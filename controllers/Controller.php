<?php

namespace Controllers;

require_once __DIR__ . '/../database/Database.php';

use Database\Database;
use PDO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{
    protected PDO $db;
    protected Environment $twig;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getConnection();
            if (!$this->db) {
                error_log('ERREUR: Pas de connexion à la base de données dans Controller');
            }
        } catch (Exception $e) {
            error_log('ERREUR de connexion à la base: ' . $e->getMessage());
        }
        
        $this->twig = require __DIR__ . '/../database/twig.php';
        
        // Générer un token CSRF s'il n'existe pas
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // Ajouter le token CSRF et la session aux variables globales de Twig
        $this->twig->addGlobal('csrf_token', $_SESSION['csrf_token']);
        $this->twig->addGlobal('session', $_SESSION);
        
        // Debug des sessions
        if (isset($_SESSION['user_id'])) {
            error_log('=== DEBUG CONTROLLER ===');
            error_log('Session active pour user_id: ' . $_SESSION['user_id']);
            error_log('Role en session: ' . ($_SESSION['role'] ?? 'non défini'));
        }
    }

    protected function redirect($path)
    {
        $path = ltrim($path, '/');
        header("Location: /" . $path);
        exit();
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function isAdmin()
    {
        error_log('=== Vérification isAdmin ===');
        error_log('Session: ' . print_r($_SESSION, true));
        
        if (!isset($_SESSION['user_id'])) {
            error_log('Pas de user_id en session');
            return false;
        }

        try {
            // Vérification directe en base de données
            $stmt = $this->db->prepare('
                SELECT u.*, r.name as role_name 
                FROM users u 
                LEFT JOIN roles r ON u.role = r.id 
                WHERE u.id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                error_log('Utilisateur non trouvé en base');
                return false;
            }
            
            $roleInt = (int)$user['role'];
            error_log('Role en base: ' . $user['role'] . ' (type: ' . gettype($user['role']) . ')');
            error_log('Role après cast: ' . $roleInt . ' (type: ' . gettype($roleInt) . ')');
            error_log('Role name: ' . $user['role_name']);
            
            // Mettre à jour la session avec les bonnes valeurs
            $_SESSION['role'] = $roleInt;
            $_SESSION['role_name'] = $user['role_name'];
            
            return $roleInt === 2;
            
        } catch (Exception $e) {
            error_log('Erreur lors de la vérification admin: ' . $e->getMessage());
            return false;
        }
    }

    protected function is_granted($role) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $user = (new \Models\UserModel())->getUserById($_SESSION['user_id']);
        if (!$user) {
            return false;
        }

        switch ($role) {
            case 'admin':
                return $user['role'] === '2';
            case 'tech':
                return $user['role'] === '1' || $user['role'] === '2';
            case 'user':
                return true; // Tout utilisateur connecté est au moins un utilisateur
            default:
                return false;
        }
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/connection');
        }
    }

    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            $this->redirect('/');
        }
    }

    protected function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }

    public function setSession(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function getSession(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function deleteSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    protected function render($view, $data = [])
    {
        // Ajouter les variables de session aux données
        $data['session'] = $_SESSION;
        
        // Ajouter les messages flash
        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            $data['error'] = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        // Rendre la vue avec Twig
        if (!str_ends_with($view, '.twig')) {
            $view .= '.twig';
        }
        return $this->twig->render($view, $data);
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getPostData()
    {
        return $_POST;
    }

    protected function getQueryData()
    {
        return $_GET;
    }

    public function checkAdmin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
            $this->redirect('/users/login');
            return false;
        }

        if (!isset($_SESSION['role']) || $_SESSION['role'] != '2') {
            $_SESSION['error'] = 'Accès réservé aux administrateurs';
            $this->redirect('/');
            return false;
        }

        return true;
    }
}
