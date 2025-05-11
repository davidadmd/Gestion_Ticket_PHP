<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests simples pour les méthodes du TicketModel
 */
class TicketModelTest extends TestCase
{
    /**
     * Test de la structure de TicketModel
     * Analyse des méthodes disponibles dans le modèle
     */
    public function testTicketModelStructure()
    {
        // Charger le fichier TicketModel.php et examiner son contenu
        $modelContent = file_get_contents(__DIR__ . '/../../models/TicketModel.php');
        
        // Vérifier la classe et l'espace de noms
        $this->assertStringContainsString('namespace Models;', $modelContent);
        $this->assertStringContainsString('class TicketModel', $modelContent);
        
        // Vérifier les méthodes principales
        $this->assertStringContainsString('public function createTicket', $modelContent);
        $this->assertStringContainsString('public function getAllTickets', $modelContent);
        $this->assertStringContainsString('public function getTicketById', $modelContent);
        $this->assertStringContainsString('public function updateTicket', $modelContent);
        $this->assertStringContainsString('public function getTicketsByUser', $modelContent);
        $this->assertStringContainsString('public function addComment', $modelContent);
        
        // Vérifier les requêtes SQL essentielles
        $this->assertStringContainsString('INSERT INTO tickets', $modelContent);
        $this->assertStringContainsString('SELECT t.id, t.title, t.description', $modelContent);
        $this->assertStringContainsString('FROM tickets t', $modelContent);
        $this->assertStringContainsString('LEFT JOIN users', $modelContent);
        $this->assertStringContainsString('UPDATE tickets SET', $modelContent);
    }
    
    /**
     * Test de la structure des requêtes SQL
     * Vérifie que les principales requêtes SQL contiennent les colonnes et tables nécessaires
     */
    public function testTicketSqlQueries()
    {
        $modelContent = file_get_contents(__DIR__ . '/../../models/TicketModel.php');
        
        // Vérifier les parties importantes des requêtes SQL
        
        // 1. Création d'un ticket
        $this->assertStringContainsString('INSERT INTO tickets (title, description, user_id, priority, status, assigned_to', $modelContent);
        
        // 2. Récupération des tickets
        $this->assertStringContainsString('SELECT t.*, u.username', $modelContent);
        $this->assertStringContainsString('WHERE t.user_id = :user_id', $modelContent);
        
        // 3. Commentaires
        $this->assertStringContainsString('INSERT INTO comments', $modelContent);
        $this->assertStringContainsString('WHERE c.ticket_id = :ticket_id', $modelContent);
        
        // 4. Mise à jour du statut
        $this->assertStringContainsString('UPDATE tickets SET status = :status', $modelContent);
    }
}
