<?php

namespace Models;

use PDO;
use Models\Database;

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Récupérer un utilisateur par son ID
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son nom d'utilisateur
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouvel utilisateur
    public function createUser($data) {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role']
        ]);
    }

    // Mettre à jour un utilisateur
    public function updateUser($id, $data) {
        $sql = "UPDATE users SET username = :username";
        $params = ['id' => $id, 'username' => $data['username']];

        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY username";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un utilisateur
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Vérifier les identifiants de connexion
    public function verifyLogin($username, $password) {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Récupérer tous les techniciens et admins
    public function getTechnicians() {
        $sql = "SELECT id, username, role FROM users WHERE role IN (1, 2) ORDER BY username";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
