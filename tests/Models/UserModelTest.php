<?php

namespace Tests\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests simples pour les méthodes du UserModel
 */
class UserModelTest extends TestCase
{
    /**
     * Test de la structure de UserModel
     * Analyse des méthodes disponibles dans le modèle
     */
    public function testUserModelStructure()
    {
        // Charger le fichier UserModel.php et examiner son contenu
        $modelContent = file_get_contents(__DIR__ . '/../../models/UserModel.php');
        
        // Vérifier la classe et l'espace de noms
        $this->assertStringContainsString('namespace Models;', $modelContent);
        $this->assertStringContainsString('class UserModel', $modelContent);
        
        // Vérifier les méthodes principales
        $this->assertStringContainsString('public function getUserById', $modelContent);
        $this->assertStringContainsString('public function getUserByUsername', $modelContent);
        $this->assertStringContainsString('public function createUser', $modelContent);
        $this->assertStringContainsString('public function updateUser', $modelContent);
        $this->assertStringContainsString('public function getAllUsers', $modelContent);
        $this->assertStringContainsString('public function deleteUser', $modelContent);
        $this->assertStringContainsString('public function verifyLogin', $modelContent);
        
        // Vérifier les requêtes SQL
        $this->assertStringContainsString('SELECT * FROM users WHERE id = :id', $modelContent);
        $this->assertStringContainsString('SELECT * FROM users WHERE username = :username', $modelContent);
        $this->assertStringContainsString('INSERT INTO users', $modelContent);
        $this->assertStringContainsString('UPDATE users SET', $modelContent);
        $this->assertStringContainsString('DELETE FROM users WHERE id = :id', $modelContent);
    }
    
    /**
     * Test de la méthode de vérification de connexion
     * Ceci simule la vérification de password_verify sans accès à la base de données
     */
    public function testPasswordVerificationLogic()
    {        
        // Vérifier que password_verify fonctionne comme prévu
        $password = 'test123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Test direct de password_verify
        $this->assertTrue(password_verify($password, $hashedPassword));
        $this->assertFalse(password_verify('wrong_password', $hashedPassword));
        
        // Ce test vérifie la logique sans réellement appeler la méthode du modèle
        // car celle-ci dépend de la base de données
    }
}
