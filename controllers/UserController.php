<?php
namespace Controllers;

use Models\UserModel;

class UserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }



    /**
     * Afficher le formulaire d'inscription et traiter l'inscription.
     */
    public function inscription()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($username) || empty($email) || empty($password)) {
                $error = "Tous les champs sont obligatoires";
                include '../views/auth/register.php';
                return;
            }

            if ($password !== $confirm_password) {
                $error = "Les mots de passe ne correspondent pas";
                include '../views/auth/register.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse email n'est pas valide";
                include '../views/auth/register.php';
                return;
            }

            // Vérifier si l'email existe déjà
            if ($this->userModel->findByEmail($email)) {
                $error = "Cette adresse email est déjà utilisée";
                include '../views/auth/register.php';
                return;
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Créer l'utilisateur
            if ($this->userModel->register($username, $email, $hashedPassword)) {
                header('Location: /connection');
                exit;
            } else {
                $error = "Une erreur est survenue lors de l'inscription";
                include '../views/auth/register.php';
            }
        } else {
            include '../views/auth/register.php';
        }
    }

    public function connection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($email) || empty($password)) {
                $error = "Tous les champs sont obligatoires";
                include '../views/auth/login.php';
                return;
            }

            // Vérifier l'utilisateur
            $user = $this->userModel->get_user_by_mail($email);
            if ($user && password_verify($password, $user->password)) {
                // Connexion réussie
                $_SESSION["mail"] = $user->mail;
                $_SESSION["username"] = $user->username;
                $_SESSION["role"] = $user->role;
                
                header('Location: /tickets');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect";
                include '../views/auth/login.php';
            }
        } else {
            include '../views/auth/login.php';
        }
    }

    public function deconnection()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function admin()
    {
        // Vérifier si l'utilisateur est connecté et est admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /connection');
            exit;
        }

        $users = $this->userModel->getAllUsers();
        include '../views/admin/dashboard.php';
    }
}
