<?php
namespace Controllers;

use Models\UserModel;

class ProfileController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function profile() {
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        if (!$user) {
            $this->redirect('/logout');
        }

        echo $this->twig->render('profile/index.twig', [
            'title' => 'Mon Profil',
            'user' => $user,
            'active_page' => 'profile'
        ]);
    }

    public function update() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $data = [
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? '',
                'current_password' => $_POST['current_password'] ?? '',
                'new_password' => $_POST['new_password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? ''
            ];

            $errors = [];

            // Validation du nom d'utilisateur
            if (empty($data['username'])) {
                $errors[] = "Le nom d'utilisateur est requis";
            }

            // Validation de l'email
            if (empty($data['email'])) {
                $errors[] = "L'adresse email est requise";
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'adresse email n'est pas valide";
            }

            // Si un nouveau mot de passe est fourni
            if (!empty($data['new_password'])) {
                if (empty($data['current_password'])) {
                    $errors[] = "Le mot de passe actuel est requis pour le changement";
                }
                if ($data['new_password'] !== $data['confirm_password']) {
                    $errors[] = "Les nouveaux mots de passe ne correspondent pas";
                }
                if (strlen($data['new_password']) < 6) {
                    $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères";
                }
            }

            if (empty($errors)) {
                $user = $this->userModel->getUserById($userId);
                
                // Vérifier le mot de passe actuel si nécessaire
                if (!empty($data['new_password'])) {
                    if (!password_verify($data['current_password'], $user['password'])) {
                        $errors[] = "Le mot de passe actuel est incorrect";
                    }
                }

                if (empty($errors)) {
                    $updateData = [
                        'username' => $data['username'],
                        'email' => $data['email']
                    ];

                    if (!empty($data['new_password'])) {
                        $updateData['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                    }

                    if ($this->userModel->updateUser($userId, $updateData)) {
                        $_SESSION['success'] = "Votre profil a été mis à jour avec succès";
                        $this->redirect('');
                    } else {
                        $errors[] = "Une erreur est survenue lors de la mise à jour du profil";
                    }
                }
            }

            if (!empty($errors)) {
                echo $this->twig->render('profile/index.twig', [
                    'title' => 'Mon Profil',
                    'user' => $data,
                    'errors' => $errors,
                    'active_page' => 'profile',
                    'session' => $_SESSION
                ]);
                return;
            }
        }

        $this->redirect('');
    }
}
