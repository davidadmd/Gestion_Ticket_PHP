<?php

namespace Controllers;

use Models\UserModel;
use Models\RoleModel;
use PDO;
use Twig\Environment; // Ajout du namespace global pour PDO

class AuthController extends Controller {
    private $userModel;
    protected Environment $twig;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    // Afficher le formulaire de connexion
    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/tickets');
            exit;
        }
        echo $this->twig->render('auth/login.twig');
    }

    // Traiter la connexion
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Tous les champs sont obligatoires';
                $this->redirect('login');
                exit;
            }

            $user = $this->userModel->verifyLogin($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = 'Connexion réussie';
                $this->redirect('/');
            } else {
                $_SESSION['error'] = 'Nom d\'utilisateur ou mot de passe incorrect';
                $this->redirect('login');
            }
            exit;
        }
    }

    // Afficher le formulaire d'inscription
    public function showRegisterForm() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/tickets');
            exit;
        }
        echo $this->twig->render('auth/register.twig');
    }

    // Traiter l'inscription
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];
            if (empty($username)) {
                $errors[] = 'Le nom d\'utilisateur est obligatoire';
            }
            if (empty($password)) {
                $errors[] = 'Le mot de passe est obligatoire';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $this->redirect('register');
                exit;
            }

            if ($this->userModel->getUserByUsername($username)) {
                $_SESSION['error'] = 'Ce nom d\'utilisateur est déjà utilisé';
                $this->redirect('register');
                exit;
            }

            if ($this->userModel->createUser($username, $password)) {
                $_SESSION['success'] = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
                $this->redirect('login');
            } else {
                $_SESSION['error'] = 'Erreur lors de la création du compte';
                $this->redirect('register');
            }
            exit;
        }
    }

    // Déconnexion
    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
}
