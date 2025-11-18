<?php
/**
 * Modelo User - Gestiona las operaciones de usuarios
 */
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo usuario
     */
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, full_name, role_id)
                VALUES (:username, :email, :password, :full_name, :role_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        $sql = "SELECT u.*, r.name as role_name, r.description as role_description,
                       r.can_upload, r.can_edit, r.can_delete, r.can_share, r.can_manage_users
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por username
     */
    public function getByUsername($username) {
        $sql = "SELECT u.*, r.name as role_name, r.description as role_description,
                       r.can_upload, r.can_edit, r.can_delete, r.can_share, r.can_manage_users
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email) {
        $sql = "SELECT u.*, r.name as role_name, r.description as role_description,
                       r.can_upload, r.can_edit, r.can_delete, r.can_share, r.can_manage_users
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los usuarios
     */
    public function getAll() {
        $sql = "SELECT u.*, r.name as role_name, r.description as role_description
                FROM users u
                JOIN roles r ON u.role_id = r.id
                ORDER BY u.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Actualizar usuario
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['username'])) {
            $fields[] = 'username = :username';
            $params['username'] = $data['username'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params['email'] = $data['email'];
        }
        if (isset($data['password'])) {
            $fields[] = 'password = :password';
            $params['password'] = $data['password'];
        }
        if (isset($data['full_name'])) {
            $fields[] = 'full_name = :full_name';
            $params['full_name'] = $data['full_name'];
        }
        if (isset($data['role_id'])) {
            $fields[] = 'role_id = :role_id';
            $params['role_id'] = $data['role_id'];
        }
        if (isset($data['is_active'])) {
            $fields[] = 'is_active = :is_active';
            $params['is_active'] = $data['is_active'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Eliminar usuario
     */
    public function delete($id) {
        // No permitir eliminar el usuario admin principal
        if ($id == 1) {
            return false;
        }
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Actualizar último login
     */
    public function updateLastLogin($id) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Verificar si existe username
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si existe email
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar contraseña
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Hash de contraseña
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
