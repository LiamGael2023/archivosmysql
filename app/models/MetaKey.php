<?php
/**
 * Modelo MetaKey - Gestiona los metadatos de archivos y carpetas
 */
class MetaKey {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Agregar un metakey
     */
    public function add($entity_type, $entity_id, $meta_key, $meta_value = null) {
        $sql = "INSERT INTO meta_keys (entity_type, entity_id, meta_key, meta_value)
                VALUES (:entity_type, :entity_id, :meta_key, :meta_value)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'meta_key' => $meta_key,
            'meta_value' => $meta_value
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener todos los metakeys de una entidad
     */
    public function getByEntity($entity_type, $entity_id) {
        $sql = "SELECT * FROM meta_keys
                WHERE entity_type = :entity_type AND entity_id = :entity_id
                ORDER BY meta_key ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un metakey específico
     */
    public function getById($id) {
        $sql = "SELECT * FROM meta_keys WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Actualizar un metakey
     */
    public function update($id, $meta_key, $meta_value = null) {
        $sql = "UPDATE meta_keys SET meta_key = :meta_key, meta_value = :meta_value
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'meta_key' => $meta_key,
            'meta_value' => $meta_value
        ]);
    }

    /**
     * Eliminar un metakey
     */
    public function delete($id) {
        $sql = "DELETE FROM meta_keys WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Eliminar todos los metakeys de una entidad
     */
    public function deleteByEntity($entity_type, $entity_id) {
        $sql = "DELETE FROM meta_keys WHERE entity_type = :entity_type AND entity_id = :entity_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'entity_type' => $entity_type,
            'entity_id' => $entity_id
        ]);
    }

    /**
     * Obtener todos los metakeys únicos
     */
    public function getAllUniqueKeys() {
        $sql = "SELECT DISTINCT meta_key FROM meta_keys ORDER BY meta_key ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtener todos los valores únicos de un metakey específico
     */
    public function getUniqueValues($meta_key) {
        $sql = "SELECT DISTINCT meta_value FROM meta_keys
                WHERE meta_key = :meta_key AND meta_value IS NOT NULL
                ORDER BY meta_value ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['meta_key' => $meta_key]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
