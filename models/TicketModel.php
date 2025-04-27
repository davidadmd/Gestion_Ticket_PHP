<?php

namespace Models;

use Database\Database;
use PDO;

class TicketModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Créer un nouveau ticket
    public function createTicket($data) {
        $sql = "INSERT INTO tickets (title, description, user_id, priority, status, assigned_to, created_at) 
                VALUES (:title, :description, :user_id, :priority, :status, :assigned_to, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => $data['user_id'],
            'priority' => $data['priority'] ?? 'moyenne',
            'status' => $data['status'] ?? 'ouvert',
            'assigned_to' => $data['assigned_to'] ?: null
        ]);
    }

    // Récupérer tous les tickets
    public function getAllTickets() {
        $sql = "SELECT t.id, t.title, t.description, t.user_id, 
                    COALESCE(t.status, 'ouvert') as status,
                    COALESCE(t.priority, 'moyenne') as priority,
                    t.assigned_to, t.created_at,
                    u.username, assigned.username as assigned_username
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id 
                LEFT JOIN users assigned ON t.assigned_to = assigned.id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un ticket par son ID
    public function getTicketById($id) {
        $sql = "SELECT t.id, t.title, t.description, t.user_id, 
                    COALESCE(t.status, 'ouvert') as status,
                    COALESCE(t.priority, 'moyenne') as priority,
                    t.assigned_to, t.created_at,
                    u.username, assigned.username as assigned_username
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id 
                LEFT JOIN users assigned ON t.assigned_to = assigned.id 
                WHERE t.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un ticket
    public function updateTicket($id, $data) {
        // Commencer la requête avec les champs obligatoires
        $sql = "UPDATE tickets SET title = :title, description = :description";
        $params = [
            'id' => $id,
            'title' => $data['title'],
            'description' => $data['description']
        ];

        // Ajouter les champs optionnels seulement s'ils sont fournis
        if (array_key_exists('priority', $data)) {
            $sql .= ", priority = :priority";
            $params['priority'] = $data['priority'];
        }

        if (array_key_exists('status', $data)) {
            $sql .= ", status = :status";
            $params['status'] = $data['status'];
        }

        if (array_key_exists('assigned_to', $data)) {
            $sql .= ", assigned_to = :assigned_to";
            $params['assigned_to'] = $data['assigned_to'] ?: null;
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Récupérer les tickets d'un utilisateur
    public function getTicketsAssignedToAdmin($adminId) {
        $sql = "SELECT t.*, u.username, assigned.username as assigned_username
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id 
                LEFT JOIN users assigned ON t.assigned_to = assigned.id
                WHERE t.assigned_to = :admin_id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['admin_id' => $adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketsByUser($userId) {
        $sql = "SELECT t.*, u.username, assigned.username as assigned_username
                FROM tickets t 
                LEFT JOIN users u ON t.user_id = u.id 
                LEFT JOIN users assigned ON t.assigned_to = assigned.id
                WHERE t.user_id = :user_id 
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un commentaire
    public function addComment($data) {
        $sql = "INSERT INTO comments (ticket_id, user_id, content, created_at) 
                VALUES (:ticket_id, :user_id, :content, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'ticket_id' => $data['ticket_id'],
            'user_id' => $data['user_id'],
            'content' => $data['content']
        ]);
    }

    // Récupérer les commentaires d'un ticket
    public function getTicketComments($ticketId) {
        $sql = "SELECT c.*, u.username 
                FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.ticket_id = :ticket_id 
                ORDER BY c.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['ticket_id' => $ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour le statut d'un ticket
    public function updateStatus($id, $status) {
        $sql = "UPDATE tickets SET status = :status WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    // Récupérer les tickets assignés à un technicien
    public function getTicketsForTechnician($technicianId) {
        $sql = "SELECT t.*, 
                    creator.username as creator_username,
                    assigned.username as assigned_username
                FROM tickets t 
                LEFT JOIN users creator ON t.user_id = creator.id
                LEFT JOIN users assigned ON t.assigned_to = assigned.id
                WHERE t.assigned_to = :technician_id
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['technician_id' => $technicianId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
