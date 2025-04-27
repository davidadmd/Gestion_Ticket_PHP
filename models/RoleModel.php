<?php

namespace Models;

use Database\Database;
use PDO;

class RoleModel extends Model
{
    const ROLE_USER = 0;
    const ROLE_TECH = 1;
    const ROLE_ADMIN = 2;

    public function __construct()
    {
        parent::__construct(Database::getInstance()->getConnection(), 'roles');
    }

    public function getAllRoles()
    {
        $sql = "SELECT * FROM roles ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoleById($id)
    {
        $sql = "SELECT * FROM roles WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRoleName($id)
    {
        $role = $this->getRoleById($id);
        return $role ? $role['name'] : null;
    }

    public static function getRoleLabel($roleId)
    {
        switch ($roleId) {
            case self::ROLE_USER:
                return 'Utilisateur';
            case self::ROLE_TECH:
                return 'Technicien';
            case self::ROLE_ADMIN:
                return 'Administrateur';
            default:
                return 'Inconnu';
        }
    }
}
