<?php
/**
 * Modelo SharedLink - Gestiona los enlaces compartidos
 */
class SharedLink {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear un enlace compartido
     */
    public function create($entity_type, $entity_id, $expires_at = null) {
        $token = $this->generateToken();

        $sql = "INSERT INTO shared_links (token, entity_type, entity_id, expires_at)
                VALUES (:token, :entity_type, :entity_id, :expires_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'token' => $token,
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'expires_at' => $expires_at
        ]);

        return $token;
    }

    /**
     * Generar un token único
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Obtener enlace por token
     */
    public function getByToken($token) {
        $sql = "SELECT * FROM shared_links WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    /**
     * Obtener todos los enlaces de una entidad
     */
    public function getByEntity($entity_type, $entity_id) {
        $sql = "SELECT * FROM shared_links
                WHERE entity_type = :entity_type AND entity_id = :entity_id
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Validar si un token está activo y no ha expirado
     */
    public function isValid($token) {
        $link = $this->getByToken($token);

        if (!$link || !$link['is_active']) {
            return false;
        }

        if ($link['expires_at'] && strtotime($link['expires_at']) < time()) {
            return false;
        }

        return true;
    }

    /**
     * Activar/Desactivar un enlace
     */
    public function toggleStatus($id, $is_active) {
        $sql = "UPDATE shared_links SET is_active = :is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'is_active' => $is_active ? 1 : 0
        ]);
    }

    /**
     * Eliminar un enlace
     */
    public function delete($id) {
        $sql = "DELETE FROM shared_links WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Eliminar enlaces expirados
     */
    public function deleteExpired() {
        $sql = "DELETE FROM shared_links
                WHERE expires_at IS NOT NULL AND expires_at < NOW()";
        $stmt = $this->db->query($sql);
        return $stmt->rowCount();
    }

    /**
     * Obtener enlace completo con información de la entidad
     */
    public function getDetailsByToken($token) {
        $sql = "SELECT sl.*,
                CASE
                    WHEN sl.entity_type = 'file' THEN f.name
                    WHEN sl.entity_type = 'folder' THEN fo.name
                END as entity_name,
                CASE
                    WHEN sl.entity_type = 'file' THEN f.original_name
                    ELSE NULL
                END as original_name,
                CASE
                    WHEN sl.entity_type = 'file' THEN f.file_path
                    ELSE NULL
                END as file_path
                FROM shared_links sl
                LEFT JOIN files f ON sl.entity_type = 'file' AND sl.entity_id = f.id
                LEFT JOIN folders fo ON sl.entity_type = 'folder' AND sl.entity_id = fo.id
                WHERE sl.token = :token";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }
}
