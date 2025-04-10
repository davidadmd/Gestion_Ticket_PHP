<?php

namespace Controllers;

use Models\UserModel;
use Models\TicketModel;

class TechnicienController extends Controller
{
    private UserModel $userModel;
    private TicketModel $ticketModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->ticketModel = new TicketModel();
    }

    public function checkTechnicien()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
            $this->redirect('/users/login');
            return false;
        }
        if (!isset($_SESSION['role']) || $_SESSION['role'] != '1') {
            return $this->render('error/403', [
                'message' => 'Accès réservé aux techniciens'
            ]);
        }
        return true;
    }

    public function tickets()
    {
        $this->checkTechnicien();
        
        $tickets = $this->ticketModel->getTicketsForTechnician($_SESSION['user_id']);
        $technicians = $this->userModel->getTechnicians();
        
        return $this->render('technicien/tickets/list', [
            'tickets' => $tickets,
            'technicians' => $technicians,
            'active_page' => 'technicien_tickets'
        ]);
    }

    public function updateTicketStatus($id)
    {
        $this->checkTechnicien();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/technicien/tickets');
            return;
        }

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || ($ticket['assigned_to'] != $_SESSION['user_id'] && !empty($ticket['assigned_to']))) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier ce ticket';
            $this->redirect('/technicien/tickets');
            return;
        }

        $status = $_POST['status'] ?? 'ouvert';
        if ($this->ticketModel->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Le statut du ticket a été mis à jour';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du statut';
        }

        $this->redirect('/technicien/tickets');
    }

    public function assignToMe($id)
    {
        $this->checkTechnicien();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/technicien/tickets');
            return;
        }

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || !empty($ticket['assigned_to'])) {
            $_SESSION['error'] = 'Ce ticket est déjà assigné';
            $this->redirect('/technicien/tickets');
            return;
        }

        if ($this->ticketModel->assignTicket($id, ['assigned_to' => $_SESSION['user_id']])) {
            $_SESSION['success'] = 'Le ticket vous a été assigné';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'assignation du ticket';
        }

        $this->redirect('/technicien/tickets');
    }

    public function edit($id)
    {
        $this->checkTechnicien();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/technicien/tickets');
            return;
        }

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || $ticket['assigned_to'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'\u00eates pas autorisé à modifier ce ticket';
            $this->redirect('/technicien/tickets');
            return;
        }

        $data = [
            'status' => $_POST['status'] ?? 'ouvert'
        ];

        if ($this->ticketModel->updateTicket($id, $data)) {
            $_SESSION['success'] = 'Le ticket a été mis à jour';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du ticket';
        }

        $this->redirect('/technicien/tickets');
    }

    public function updateStatus($id)
    {
        $this->checkTechnicien();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/technicien/tickets');
            return;
        }

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || $ticket['assigned_to'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous n\'\u00eates pas autorisé à modifier ce ticket';
            $this->redirect('/technicien/tickets');
            return;
        }

        $status = $_POST['status'] ?? null;
        if (!$status) {
            $_SESSION['error'] = 'Le statut est requis';
            $this->redirect('/technicien/tickets');
            return;
        }

        if ($this->ticketModel->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Le statut du ticket a été mis à jour';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du statut';
        }

        $this->redirect('/technicien/tickets');
    }

    public function editTicket($id)
    {
        $this->checkTechnicien();

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || ($ticket['assigned_to'] != $_SESSION['user_id'] && !empty($ticket['assigned_to']))) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier ce ticket';
            $this->redirect('/technicien/tickets');
            return;
        }

        return $this->render('technicien/tickets/edit', [
            'ticket' => $ticket,
            'active_page' => 'tickets'
        ]);
    }

    public function updateTicket($id)
    {
        $this->checkTechnicien();

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité : formulaire invalide';
            $this->redirect('/technicien/tickets');
            return;
        }

        $ticket = $this->ticketModel->getTicketById($id);
        if (!$ticket || ($ticket['assigned_to'] != $_SESSION['user_id'] && !empty($ticket['assigned_to']))) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier ce ticket';
            $this->redirect('/technicien/tickets');
            return;
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'basse',
            'status' => $_POST['status'] ?? 'ouvert'
        ];

        if ($this->ticketModel->updateTicket($id, $data)) {
            $_SESSION['success'] = 'Le ticket a été mis à jour';
        } else {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du ticket';
        }

        $this->redirect('/technicien/tickets');
    }
}
