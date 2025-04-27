<?php

namespace Controllers;

use Models\UserModel;
use Models\RoleModel;
use Models\TicketModel;

class AdminController extends Controller
{
    private UserModel $userModel;
    private RoleModel $roleModel;
    private TicketModel $ticketModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->ticketModel = new TicketModel();
        
        // Vérifier que l'utilisateur est admin
        if (!isset($_SESSION['user_id']) || !$this->isAdmin()) {
            $this->redirect('/');
        }
    }

    public function index()
    {
        echo $this->twig->render('admin/dashboard.twig', [
            'title' => 'Administration',
            'active_page' => 'admin'
        ]);
    }

    public function settings()
    {
        echo $this->twig->render('admin/settings.twig', [
            'title' => 'Paramètres',
            'active_page' => 'admin'
        ]);
    }



    // Liste des utilisateurs
    public function listUsers()
    {
        $users = $this->userModel->getAllUsers();
        
        echo $this->twig->render('admin/users/list.twig', [
            'users' => $users,
            'active_page' => 'admin',
            'session' => $_SESSION
        ]);
    }

    // Formulaire de création d'utilisateur
    public function newUser()
    {
        $twig = $this->twig;
        echo $twig->render('admin/users/edit.twig', [
            'active_page' => 'admin',
            'session' => $_SESSION
        ]);
    }

    // Création d'un utilisateur
    public function createUser()
    {
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user'
        ];

        $errors = [];

        // Validation
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        }
        if (empty($data['email'])) {
            $errors[] = "L'email est requis";
        }
        if (empty($data['password'])) {
            $errors[] = "Le mot de passe est requis";
        }
        if ($data['password'] !== ($_POST['password_confirm'] ?? '')) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        if (empty($errors)) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            if ($this->userModel->createUser($data)) {
                $_SESSION['success'] = "L'utilisateur a été créé avec succès";
                $this->redirect('/admin/users');
                return;
            }
            $errors[] = "Une erreur est survenue lors de la création de l'utilisateur";
        }

        $twig = $this->twig;
        echo $twig->render('admin/users/edit.twig', [
            'errors' => $errors,
            'old' => $data,
            'active_page' => 'admin',
            'session' => $_SESSION
        ]);
    }

    // Formulaire d'édition d'utilisateur
    public function editUser($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            $this->redirect('/admin/users');
            return;
        }

        $twig = $this->twig;
        echo $twig->render('admin/users/edit.twig', [
            'user' => $user,
            'active_page' => 'admin',
            'session' => $_SESSION
        ]);
    }

    // Mise à jour d'un utilisateur
    public function updateUser($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            $this->redirect('/admin/users');
            return;
        }

        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'user'
        ];

        $errors = [];

        // Validation
        if (empty($data['username'])) {
            $errors[] = "Le nom d'utilisateur est requis";
        }
        if (empty($data['email'])) {
            $errors[] = "L'email est requis";
        }

        if (empty($errors)) {
            if ($this->userModel->updateUser($id, $data)) {
                $_SESSION['success'] = "L'utilisateur a été mis à jour avec succès";
                $this->redirect('/admin/users');
                return;
            }
            $errors[] = "Une erreur est survenue lors de la mise à jour de l'utilisateur";
        }

        $twig = $this->twig;
        echo $twig->render('admin/users/edit.twig', [
            'user' => array_merge($user, $data),
            'errors' => $errors,
            'active_page' => 'admin',
            'session' => $_SESSION
        ]);
    }

    // Suppression d'un utilisateur
    public function deleteUser($id)
    {
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
            $this->redirect('/admin/users');
            return;
        }

        if ($this->userModel->deleteUser($id)) {
            $_SESSION['success'] = "L'utilisateur a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'utilisateur";
        }

        $this->redirect('/admin/users');
    }

    public function tickets() {
        $tickets = $this->ticketModel->getAllTickets();
        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();
        
        echo $this->twig->render('admin/tickets.twig', [
            'title' => 'Gestion des tickets',
            'active_page' => 'admin',
            'tickets' => $tickets,
            'status_labels' => $statusInfo['labels'],
            'status_classes' => $statusInfo['classes'],
            'priority_labels' => $priorityInfo['labels'],
            'priority_classes' => $priorityInfo['classes'],
            'session' => $_SESSION
        ]);
    }

    private function getStatusInfo() {
        return [
            'labels' => [
                'ouvert' => 'Ouvert',
                'en_cours' => 'En cours',
                'resolu' => 'Résolu',
                'ferme' => 'Fermé',
                'null' => 'Non défini'
            ],
            'classes' => [
                'ouvert' => 'bg-danger',
                'en_cours' => 'bg-warning text-dark',
                'resolu' => 'bg-info',
                'ferme' => 'bg-success',
                'null' => 'bg-secondary'
            ]
        ];
    }

    private function getPriorityInfo() {
        return [
            'labels' => [
                'basse' => 'Basse',
                'moyenne' => 'Moyenne',
                'haute' => 'Haute',
                'null' => 'Non définie'
            ],
            'classes' => [
                'basse' => 'bg-info',
                'moyenne' => 'bg-warning text-dark',
                'haute' => 'bg-danger',
                'null' => 'bg-secondary'
            ]
        ];
    }

    public function assignedTickets() {
        $tickets = $this->ticketModel->getTicketsAssignedToAdmin($_SESSION['user_id']);
        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();
        
        echo $this->twig->render('admin/tickets/assigned.twig', [
            'title' => 'Tickets assignés',
            'active_page' => 'admin_assigned_tickets',
            'tickets' => $tickets,
            'status_labels' => $statusInfo['labels'],
            'status_classes' => $statusInfo['classes'],
            'priority_labels' => $priorityInfo['labels'],
            'priority_classes' => $priorityInfo['classes'],
            'session' => [
                'role' => $_SESSION['role'],
                'user_id' => $_SESSION['user_id']
            ]
        ]);
    }

    public function deleteTicket($id) {
        $this->checkAdmin();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/admin/tickets');
            return;
        }

        if ($this->ticketModel->deleteTicket($id)) {
            $_SESSION['success'] = 'Le ticket a été supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression du ticket';
        }
        
        $this->redirect('/admin/tickets');
    }

    public function updateTicketStatus($id) {
        $this->checkAdmin();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/admin/tickets');
            return;
        }

        $status = $_POST['status'] ?? 'ouvert';
        if ($this->ticketModel->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Le statut du ticket a été mis à jour';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du statut';
        }

        $this->redirect('/admin/tickets');
    }

    public function editTicket($id) {
        $this->checkAdmin();
        
        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket) {
            $_SESSION['error'] = 'Ticket non trouvé';
            $this->redirect('/admin/tickets');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
                $this->redirect('/admin/tickets');
                return;
            }

            $data = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'priority' => $_POST['priority'] ?? '',
                'status' => $_POST['status'] ?? '',
                'assigned_to' => $_POST['assigned_to'] ?? null
            ];

            if ($this->ticketModel->updateTicket($id, $data)) {
                $_SESSION['success'] = 'Le ticket a été mis à jour';
                $this->redirect('/admin/tickets');
                return;
            } else {
                $_SESSION['error'] = 'Erreur lors de la mise à jour du ticket';
            }
        }

        $technicians = $this->userModel->getTechnicians();
        echo $this->twig->render('admin/tickets/edit.twig', [
            'ticket' => $ticket,
            'technicians' => $technicians,
            'active_page' => 'admin'
        ]);
    }

    public function assignTicketForm($id) {
        $this->checkAdmin();
        
        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket) {
            $_SESSION['error'] = 'Ticket non trouvé';
            $this->redirect('/admin/tickets');
            return;
        }

        $technicians = $this->userModel->getTechnicians();
        
        return $this->render('admin/tickets/assign', [
            'ticket' => $ticket,
            'technicians' => $technicians,
            'active_page' => 'admin'
        ]);
    }

    public function assignTicket($id) {
        $this->checkAdmin();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/admin/tickets');
            return;
        }

        $assignedTo = $_POST['assigned_to'] ?? null;
        if ($this->ticketModel->assignTicket($id, ['assigned_to' => $assignedTo])) {
            $_SESSION['success'] = 'Le ticket a été assigné avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'assignation du ticket';
        }

        $this->redirect('/admin/tickets');
    }
}
