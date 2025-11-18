<?php
/**
 * Modelo Role - Gestiona los roles de usuario
 */
class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todos los roles
     */
    public function getAll() {
        $sql = "SELECT * FROM roles ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtener rol por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener rol por nombre
     */
    public function getByName($name) {
        $sql = "SELECT * FROM roles WHERE name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['name' => $name]);
        return $stmt->fetch();
    }

    /**
     * Crear un nuevo rol
     */
    public function create($data) {
        $sql = "INSERT INTO roles (name, description, can_upload, can_edit, can_delete, can_share, can_manage_users)
                VALUES (:name, :description, :can_upload, :can_edit, :can_delete, :can_share, :can_manage_users)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar un rol
     */
    public function update($id, $data) {
        $sql = "UPDATE roles SET
                name = :name,
                description = :description,
                can_upload = :can_upload,
                can_edit = :can_edit,
                can_delete = :can_delete,
                can_share = :can_share,
                can_manage_users = :can_manage_users
                WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Eliminar un rol
     */
    public function delete($id) {
        // No permitir eliminar el rol admin
        if ($id == 1) {
            return false;
        }
        $sql = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
