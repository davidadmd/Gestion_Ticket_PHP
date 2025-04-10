<?php

namespace Controllers;

use Models\TicketModel;
use Models\UserModel;
use Twig\Environment;

class TicketController extends Controller {
    private $ticketModel;
    private $userModel;
    protected Environment $twig;

    public function __construct() {
        parent::__construct();
        $this->ticketModel = new TicketModel();
        $this->userModel = new UserModel();
        $this->checkAuth();
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    // Liste des tickets
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

    public function index() {
        $tickets = $this->ticketModel->getAllTickets();
        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();

        echo $this->render('tickets/list', [
            'title' => 'Liste des tickets',
            'tickets' => $tickets,
            'active_page' => 'tickets',
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

    // Afficher le formulaire de création
    public function myTickets() {
        $tickets = $this->ticketModel->getTicketsByUser($_SESSION['user_id']);
        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();

        echo $this->render('tickets/my_tickets', [
            'title' => 'Mes tickets',
            'tickets' => $tickets,
            'active_page' => 'my_tickets',
            'status_labels' => $statusInfo['labels'],
            'status_classes' => $statusInfo['classes'],
            'priority_labels' => $priorityInfo['labels'],
            'priority_classes' => $priorityInfo['classes'],
            'session' => [
                'role' => $_SESSION['role'] ?? '0',
                'user_id' => $_SESSION['user_id'] ?? null
            ]
        ]);
    }

    public function showCreateForm() {
        $technicians = $this->userModel->getTechnicians();
        
        echo $this->render('tickets/create', [
            'title' => 'Créer un ticket',
            'active_page' => 'create_ticket',
            'users' => $technicians
        ]);
    }

    // Créer un ticket
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'moyenne';

            if (empty($title) || empty($description)) {
                $_SESSION['error'] = 'Le titre et la description sont obligatoires';
                $this->redirect('/tickets/create');
                exit;
            }

            $data = [
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'user_id' => $_SESSION['user_id'],
                'assigned_to' => null
            ];

            // Seuls les admins et techniciens peuvent assigner des tickets
            if ($_SESSION['role'] != '0' && isset($_POST['assigned_to']) && !empty($_POST['assigned_to'])) {
                $data['assigned_to'] = $_POST['assigned_to'];
            }

            if ($this->ticketModel->createTicket($data)) {
                $_SESSION['success'] = 'Ticket créé avec succès';
                $this->redirect('/tickets/my');
            } else {
                $_SESSION['error'] = 'Erreur lors de la création du ticket';
                $this->redirect('/tickets/create');
            }
            exit;
        }
    }

    // Voir un ticket
    public function view($id) {
        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket) {
            $this->redirect('tickets');
            exit;
        }

        $comments = $this->ticketModel->getTicketComments($id);
        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();

        echo $this->render('tickets/view', [
            'title' => 'Ticket #' . $id,
            'ticket' => $ticket,
            'comments' => $comments,
            'active_page' => 'tickets',
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

    // Afficher le formulaire de modification
    public function showEditForm($id) {
        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket) {
            $this->redirect('tickets');
            exit;
        }

        // Vérifier que l'utilisateur a le droit de modifier ce ticket
        if ($_SESSION['role'] == '0' && $ticket['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous n'avez pas le droit de modifier ce ticket";
            $this->redirect('tickets');
            exit;
        }

        $statusInfo = $this->getStatusInfo();
        $priorityInfo = $this->getPriorityInfo();

        $technicians = $this->userModel->getTechnicians();

        echo $this->render('tickets/edit', [
            'title' => 'Modifier le ticket #' . $id,
            'ticket' => $ticket,
            'active_page' => 'tickets',
            'status_labels' => $statusInfo['labels'],
            'status_classes' => $statusInfo['classes'],
            'priority_labels' => $priorityInfo['labels'],
            'priority_classes' => $priorityInfo['classes'],
            'users' => $technicians,
            'session' => [
                'role' => $_SESSION['role'] ?? 'user'
            ]
        ]);
    }

    // Modifier un ticket
    public function edit($id) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }

        // Vérifier que l'utilisateur a le droit de modifier ce ticket
        $ticket = $this->ticketModel->getTicketById($id);
        if ($_SESSION['role'] == '0' && $ticket['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Vous n'avez pas le droit de modifier ce ticket";
            $this->redirect('tickets');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $priority = $_POST['priority'] ?? 'moyenne';
            $status = $_POST['status'] ?? 'ouvert';

            if (empty($title) || empty($description)) {
                $_SESSION['error'] = 'Le titre et la description sont obligatoires';
                $this->redirect("/tickets/edit/$id");
                exit;
            }

            // Pour un utilisateur normal, on ne modifie que le titre et la description
            $data = [
                'title' => $title,
                'description' => $description
            ];

            // Seuls les admins et techniciens peuvent modifier le statut et la priorité
            if ($_SESSION['role'] != '0') {
                $data['priority'] = $priority;
                $data['status'] = $status;
                $data['assigned_to'] = $_POST['assigned_to'] ?: null;
            }

            if ($this->ticketModel->updateTicket($id, $data)) {
                $_SESSION['success'] = 'Ticket modifié avec succès';
                $this->redirect('/tickets/my');
            } else {
                $_SESSION['error'] = 'Erreur lors de la modification du ticket';
                $this->redirect("/tickets/edit/$id");
            }
            exit;
        }
    }

    // Ajouter un commentaire
    public function addComment($ticketId) {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = trim($_POST['content'] ?? '');

            if (empty($content)) {
                $_SESSION['error'] = 'Le commentaire ne peut pas être vide';
                $this->redirect("/tickets/view/$ticketId");
                exit;
            }

            $data = [
                'ticket_id' => $ticketId,
                'user_id' => $_SESSION['user_id'],
                'content' => $content
            ];

            if ($this->ticketModel->addComment($data)) {
                $_SESSION['success'] = 'Commentaire ajouté avec succès';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'ajout du commentaire';
            }

            $this->redirect("/tickets/view/$ticketId");
            exit;
        }
    }
}