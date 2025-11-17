<?php
/**
 * Modelo File - Gestiona las operaciones de archivos
 */
class File {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Crear un nuevo archivo
     */
    public function create($data) {
        $sql = "INSERT INTO files (name, original_name, file_path, extension, size, mime_type, folder_id)
                VALUES (:name, :original_name, :file_path, :extension, :size, :mime_type, :folder_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    /**
     * Obtener archivo por ID
     */
    public function getById($id) {
        $sql = "SELECT f.*, fo.name as folder_name
                FROM files f
                LEFT JOIN folders fo ON f.folder_id = fo.id
                WHERE f.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Obtener archivos por carpeta
     */
    public function getByFolder($folder_id = null, $orderBy = 'name', $orderDir = 'ASC') {
        $validOrders = ['id', 'name', 'created_at', 'size', 'extension'];
        $orderBy = in_array($orderBy, $validOrders) ? $orderBy : 'name';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT f.*, fo.name as folder_name
                FROM files f
                LEFT JOIN folders fo ON f.folder_id = fo.id
                WHERE f.folder_id " .
                ($folder_id === null ? "IS NULL" : "= :folder_id") .
                " ORDER BY f.{$orderBy} {$orderDir}";

        $stmt = $this->db->prepare($sql);
        if ($folder_id !== null) {
            $stmt->execute(['folder_id' => $folder_id]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Buscar archivos por nombre o metakeys
     */
    public function search($query, $orderBy = 'name', $orderDir = 'ASC') {
        $validOrders = ['id', 'name', 'created_at', 'size'];
        $orderBy = in_array($orderBy, $validOrders) ? $orderBy : 'name';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT DISTINCT f.*, fo.name as folder_name
                FROM files f
                LEFT JOIN folders fo ON f.folder_id = fo.id
                LEFT JOIN meta_keys mk ON mk.entity_type = 'file' AND mk.entity_id = f.id
                WHERE f.name LIKE :query
                   OR f.original_name LIKE :query
                   OR mk.meta_key LIKE :query
                   OR mk.meta_value LIKE :query
                ORDER BY f.{$orderBy} {$orderDir}";

        $stmt = $this->db->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->execute(['query' => $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Filtrar archivos por metakey
     */
    public function filterByMetaKey($metaKey, $metaValue = null, $orderBy = 'name', $orderDir = 'ASC') {
        $validOrders = ['id', 'name', 'created_at', 'size'];
        $orderBy = in_array($orderBy, $validOrders) ? $orderBy : 'name';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        if ($metaValue !== null) {
            $sql = "SELECT DISTINCT f.*, fo.name as folder_name
                    FROM files f
                    LEFT JOIN folders fo ON f.folder_id = fo.id
                    INNER JOIN meta_keys mk ON mk.entity_type = 'file' AND mk.entity_id = f.id
                    WHERE mk.meta_key = :meta_key AND mk.meta_value = :meta_value
                    ORDER BY f.{$orderBy} {$orderDir}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['meta_key' => $metaKey, 'meta_value' => $metaValue]);
        } else {
            $sql = "SELECT DISTINCT f.*, fo.name as folder_name
                    FROM files f
                    LEFT JOIN folders fo ON f.folder_id = fo.id
                    INNER JOIN meta_keys mk ON mk.entity_type = 'file' AND mk.entity_id = f.id
                    WHERE mk.meta_key = :meta_key
                    ORDER BY f.{$orderBy} {$orderDir}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['meta_key' => $metaKey]);
        }

        return $stmt->fetchAll();
    }

    /**
     * Actualizar nombre de archivo
     */
    public function updateName($id, $name) {
        $sql = "UPDATE files SET name = :name WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $name
        ]);
    }

    /**
     * Eliminar archivo
     */
    public function delete($id) {
        // Primero obtenemos la información del archivo para eliminar el físico
        $file = $this->getById($id);
        if ($file && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        $sql = "DELETE FROM files WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Obtener todos los archivos
     */
    public function getAll($orderBy = 'name', $orderDir = 'ASC') {
        $validOrders = ['id', 'name', 'created_at', 'size', 'extension'];
        $orderBy = in_array($orderBy, $validOrders) ? $orderBy : 'name';
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT f.*, fo.name as folder_name
                FROM files f
                LEFT JOIN folders fo ON f.folder_id = fo.id
                ORDER BY f.{$orderBy} {$orderDir}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
