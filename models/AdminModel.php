<?php

namespace Models;

use Database\Database;
use PDO;

class AdminModel extends Model
{
    public function __construct()
    {
        parent::__construct(Database::getInstance()->getConnection(), 'users');
    }

    public function isAdmin($userId)
    {
        $sql = "SELECT role FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && (int)$result['role'] === 2; // 2 = admin role
    }

    public function getAdminStats()
    {
        // Statistiques générales
        $stats = [
            'total_users' => $this->getTotalUsers(),
            'total_tickets' => $this->getTotalTickets(),
            'tickets_by_status' => $this->getTicketsByStatus(),
            'tickets_by_priority' => $this->getTicketsByPriority(),
            'recent_activities' => $this->getRecentActivities()
        ];

        return $stats;
    }

    private function getTotalUsers()
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getTotalTickets()
    {
        $sql = "SELECT COUNT(*) as count FROM tickets";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getTicketsByStatus()
    {
        $sql = "SELECT status, COUNT(*) as count 
                FROM tickets 
                GROUP BY status";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTicketsByPriority()
    {
        $sql = "SELECT priority, COUNT(*) as count 
                FROM tickets 
                GROUP BY priority";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getRecentActivities()
    {
        $sql = "SELECT t.id, t.title, t.status, t.created_at, u.username
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                ORDER BY t.created_at DESC
                LIMIT 10";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $sql = "SELECT u.*, 
                COUNT(DISTINCT t.id) as total_tickets,
                COUNT(DISTINCT CASE WHEN t.status = 'ouvert' THEN t.id END) as open_tickets
                FROM users u
                LEFT JOIN tickets t ON u.id = t.user_id
                GROUP BY u.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserRole($userId, $newRole)
    {
        $sql = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':role' => $newRole,
            ':id' => $userId
        ]);
    }
}
